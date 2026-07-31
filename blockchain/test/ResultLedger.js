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

  it("anchors a result snapshot and verifies it matches", async function () {
    const { ledger } = await deployFixture();
    const resultId = 1;
    const data = "2102302219987|COU08203|35|43|78|3";

    await expect(ledger.anchorResult(resultId, data))
      .to.emit(ledger, "ResultAnchored")
      .withArgs(resultId, hashOf(data));

    expect(await ledger.verifyResult(resultId, data)).to.equal(true);
    expect(await ledger.getAnchoredData(resultId)).to.equal(data);
  });

  it("fails verification when the result was tampered with off-chain", async function () {
    const { ledger } = await deployFixture();
    const resultId = 1;
    const original = "2102302219987|COU08203|35|43|78|3";
    const tampered = "2102302219987|COU08203|35|63|98|1";

    await ledger.anchorResult(resultId, original);

    expect(await ledger.verifyResult(resultId, tampered)).to.equal(false);
    // The original snapshot is still readable back, even though the DB row was tampered.
    expect(await ledger.getAnchoredData(resultId)).to.equal(original);
  });

  it("rejects anchoring from a non-owner account", async function () {
    const { ledger, other } = await deployFixture();

    await expect(
      ledger.connect(other).anchorResult(1, "irrelevant")
    ).to.be.revertedWith("ResultLedger: caller is not the owner");
  });

  it("re-anchoring the same resultId overwrites the previous snapshot", async function () {
    const { ledger } = await deployFixture();
    const resultId = 5;
    const first = "reg|mod|10|10|20|5";
    const corrected = "reg|mod|30|40|70|2";

    await ledger.anchorResult(resultId, first);
    await ledger.anchorResult(resultId, corrected);

    expect(await ledger.verifyResult(resultId, first)).to.equal(false);
    expect(await ledger.verifyResult(resultId, corrected)).to.equal(true);
    expect(await ledger.getAnchoredData(resultId)).to.equal(corrected);
  });
});
