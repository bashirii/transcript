const { expect } = require("chai");
const { ethers } = require("hardhat");

describe("ResultLedger", function () {
  async function deployFixture() {
    const [owner, other] = await ethers.getSigners();
    const ResultLedger = await ethers.getContractFactory("ResultLedger");
    const ledger = await ResultLedger.deploy();
    return { ledger, owner, other };
  }

  function hashOf(text) {
    return ethers.keccak256(ethers.toUtf8Bytes(text));
  }

  it("sets the deployer as owner", async function () {
    const { ledger, owner } = await deployFixture();
    expect(await ledger.owner()).to.equal(owner.address);
  });

  it("anchors a result hash and verifies it matches", async function () {
    const { ledger } = await deployFixture();
    const resultId = 1;
    const hash = hashOf("student=2102302219987|module=COU08203|total=78");

    await expect(ledger.anchorResult(resultId, hash))
      .to.emit(ledger, "ResultAnchored")
      .withArgs(resultId, hash);

    expect(await ledger.verifyResult(resultId, hash)).to.equal(true);
    expect(await ledger.getAnchoredHash(resultId)).to.equal(hash);
  });

  it("fails verification when the result was tampered with off-chain", async function () {
    const { ledger } = await deployFixture();
    const resultId = 1;
    const originalHash = hashOf("student=2102302219987|module=COU08203|total=78");
    const tamperedHash = hashOf("student=2102302219987|module=COU08203|total=98");

    await ledger.anchorResult(resultId, originalHash);

    expect(await ledger.verifyResult(resultId, tamperedHash)).to.equal(false);
  });

  it("rejects anchoring from a non-owner account", async function () {
    const { ledger, other } = await deployFixture();
    const hash = hashOf("irrelevant");

    await expect(
      ledger.connect(other).anchorResult(1, hash)
    ).to.be.revertedWith("ResultLedger: caller is not the owner");
  });

  it("re-anchoring the same resultId overwrites the previous hash", async function () {
    const { ledger } = await deployFixture();
    const resultId = 5;
    const firstHash = hashOf("first version");
    const correctedHash = hashOf("corrected version");

    await ledger.anchorResult(resultId, firstHash);
    await ledger.anchorResult(resultId, correctedHash);

    expect(await ledger.verifyResult(resultId, firstHash)).to.equal(false);
    expect(await ledger.verifyResult(resultId, correctedHash)).to.equal(true);
  });
});
