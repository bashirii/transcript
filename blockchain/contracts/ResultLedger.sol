// SPDX-License-Identifier: MIT
pragma solidity ^0.8.28;

/// @notice Anchors a hash of each finalized exam result on-chain so any later
/// tampering with the off-chain (Laravel/MySQL) record can be detected.
contract ResultLedger {
    address public owner;

    mapping(uint256 => bytes32) private resultHashes;

    event ResultAnchored(uint256 indexed resultId, bytes32 resultHash);

    modifier onlyOwner() {
        require(msg.sender == owner, "ResultLedger: caller is not the owner");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    /// @notice Store the hash of a result. Re-anchoring the same resultId
    /// overwrites the stored hash (used when a lecturer corrects a result).
    function anchorResult(uint256 resultId, bytes32 resultHash) external onlyOwner {
        resultHashes[resultId] = resultHash;
        emit ResultAnchored(resultId, resultHash);
    }

    /// @notice Check whether a given hash matches what's anchored for resultId.
    function verifyResult(uint256 resultId, bytes32 resultHash) external view returns (bool) {
        return resultHashes[resultId] == resultHash;
    }

    /// @notice Read back the anchored hash directly (0x0 means never anchored).
    function getAnchoredHash(uint256 resultId) external view returns (bytes32) {
        return resultHashes[resultId];
    }
}
