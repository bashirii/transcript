<?php

namespace App\Services;

use RuntimeException;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;

/// Anchors a hash of a student's result on-chain (ResultLedger.sol) and lets the app
/// re-verify, later, that the currently-stored result still matches what was anchored.
class BlockchainAnchorService
{
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
    /// Any change to any of these fields changes the hash.
    public function hashForResult(array $result): string
    {
        $canonical = implode('|', [
            $result['student_reg_no'],
            $result['module_code'],
            $result['c_a_marks'],
            $result['f_e_marks'],
            $result['total_marks'],
            $result['grade_id'],
        ]);

        return Utils::sha3($canonical);
    }

    /// Writes the result's hash on-chain. Returns the hash and the anchoring tx hash.
    public function anchor(int $resultId, array $result): array
    {
        $hash = $this->hashForResult($result);
        $txHash = null;
        $error = null;

        $this->contract->send(
            'anchorResult',
            $resultId,
            $hash,
            ['from' => $this->anchorAddress, 'gas' => '0x30000'],
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

        return ['hash' => $hash, 'tx_hash' => $txHash];
    }

    /// Recomputes the hash from the result's CURRENT field values and asks the contract
    /// whether that matches what was anchored — this is what actually detects tampering.
    public function verify(int $resultId, array $result): bool
    {
        $hash = $this->hashForResult($result);
        $matches = false;
        $error = null;

        $this->contract->call(
            'verifyResult',
            $resultId,
            $hash,
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
}
