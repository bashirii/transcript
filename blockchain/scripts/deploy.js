const { ethers, artifacts } = require("hardhat");
const fs = require("fs");
const path = require("path");

async function main() {
  const [deployer] = await ethers.getSigners();

  const ResultLedger = await ethers.getContractFactory("ResultLedger");
  const ledger = await ResultLedger.deploy();
  await ledger.waitForDeployment();

  const address = await ledger.getAddress();
  const artifact = await artifacts.readArtifact("ResultLedger");

  console.log("ResultLedger deployed to:", address);
  console.log("Deployer (owner) address:", deployer.address);

  const outDir = path.join(__dirname, "..", "deployments");
  fs.mkdirSync(outDir, { recursive: true });
  fs.writeFileSync(
    path.join(outDir, "localhost.json"),
    JSON.stringify(
      {
        address,
        deployer: deployer.address,
        abi: artifact.abi,
      },
      null,
      2
    )
  );

  console.log("Wrote deployment info to blockchain/deployments/localhost.json");
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
