// SPDX-License-Identifier: MIT
pragma solidity ^0.8.28;

/// @notice Anchors a snapshot of each finalized exam result on-chain so any later
/// tampering with the off-chain (Laravel/MySQL) record can be detected — and, since
/// the snapshot itself (not just its hash) is stored, the original values can be
/// read back and shown when a mismatch is found.
contract ResultLedger {
    address public owner;

    mapping(uint256 => string) private anchoredData;

    event ResultAnchored(uint256 indexed resultId, bytes32 dataHash);

    modifier onlyOwner() {
        require(msg.sender == owner, "ResultLedger: caller is not the owner");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    /// @notice Store the canonical data snapshot for a result. Re-anchoring the same
    /// resultId overwrites the previous snapshot (used when a lecturer corrects a result).
    function anchorResult(uint256 resultId, string calldata data) external onlyOwner {
        anchoredData[resultId] = data;
        emit ResultAnchored(resultId, keccak256(bytes(data)));
    }

    /// @notice Check whether the given data still matches what's anchored for resultId.
    function verifyResult(uint256 resultId, string calldata data) external view returns (bool) {
        return keccak256(bytes(anchoredData[resultId])) == keccak256(bytes(data));
    }

    /// @notice Read back the original anchored snapshot (empty string means never anchored).
    function getAnchoredData(uint256 resultId) external view returns (string memory) {
        return anchoredData[resultId];
    }
}
