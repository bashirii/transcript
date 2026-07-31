<?php

namespace App\Services;

use RuntimeException;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;

/// Anchors a student's result snapshot on-chain (ResultLedger.sol) and lets the app
/// re-verify, later, that the currently-stored result still matches what was anchored —
/// and, since the snapshot itself is stored on-chain (not just a hash of it), recover the
/// original values when a mismatch shows the off-chain row was tampered with.
class BlockchainAnchorService
{
    /// Order must stay fixed — it's the same order used to rebuild fields from a stored snapshot.
    protected const FIELDS = ['student_reg_no', 'module_code', 'c_a_marks', 'f_e_marks', 'total_marks', 'grade_id'];

    protected Contract $contract;

    protected string $anchorAddress;

    public function __construct()
    {
        $deployment = json_decode(file_get_contents(config('blockchain.deployment_file')), true);

        $address = config('blockchain.result_ledger_address') ?: $deployment['address'];
        $this->anchorAddress = config('blockchain.anchor_address') ?: $deployment['deployer'];

        // Default web3.php HTTP timeout is 1s, too short for a local Hardhat node under load.
        $provider = new HttpProvider(new HttpRequestManager(config('blockchain.rpc_url'), 10));
        $this->contract = (new Contract($provider, $deployment['abi']))->at($address);
    }

    /// Canonical, order-fixed representation of the fields that make a result what it is.
    /// This exact string is what gets stored on-chain and later read back.
    public function canonicalSnapshot(array $result): string
    {
        return implode('|', array_map(fn ($field) => $result[$field], self::FIELDS));
    }

    /// A short fingerprint of the snapshot, purely for local display (e.g. the "anchored_hash"
    /// column) — verification itself compares the on-chain snapshot string, not this hash.
    public function fingerprint(array $result): string
    {
        return Utils::sha3($this->canonicalSnapshot($result));
    }

    /// Writes the result's full snapshot on-chain. Returns the fingerprint (for display)
    /// and the anchoring tx hash.
    public function anchor(int $resultId, array $result): array
    {
        $snapshot = $this->canonicalSnapshot($result);
        $txHash = null;
        $error = null;

        $this->contract->send(
            'anchorResult',
            $resultId,
            $snapshot,
            ['from' => $this->anchorAddress, 'gas' => '0x50000'],
            function ($err, $tx) use (&$txHash, &$error) {
                if ($err !== null) {
                    $error = $err;
                    return;
                }
                $txHash = $tx;
            }
        );

        if ($error !== null) {
            throw new RuntimeException('Failed to anchor result on-chain: '.$error->getMessage());
        }

        return ['hash' => Utils::sha3($snapshot), 'tx_hash' => $txHash];
    }

    /// Recomputes the snapshot from the result's CURRENT field values and asks the contract
    /// whether that still matches what was anchored — this is what actually detects tampering.
    public function verify(int $resultId, array $result): bool
    {
        $snapshot = $this->canonicalSnapshot($result);
        $matches = false;
        $error = null;

        $this->contract->call(
            'verifyResult',
            $resultId,
            $snapshot,
            function ($err, $response) use (&$matches, &$error) {
                if ($err !== null) {
                    $error = $err;
                    return;
                }
                $matches = (bool) $response[0];
            }
        );

        if ($error !== null) {
            throw new RuntimeException('Failed to verify result on-chain: '.$error->getMessage());
        }

        return $matches;
    }

    /// Reads the original anchored snapshot back from the chain and rebuilds it into the
    /// same field names anchor()/verify() take. Null if resultId was never anchored.
    public function getOriginalData(int $resultId): ?array
    {
        $snapshot = null;
        $error = null;

        $this->contract->call(
            'getAnchoredData',
            $resultId,
            function ($err, $response) use (&$snapshot, &$error) {
                if ($err !== null) {
                    $error = $err;
                    return;
                }
                $snapshot = $response[0];
            }
        );

        if ($error !== null) {
            throw new RuntimeException('Failed to read anchored data from chain: '.$error->getMessage());
        }

        if (empty($snapshot)) {
            return null;
        }

        return array_combine(self::FIELDS, explode('|', $snapshot));
    }
}
