# Transcript — Student Information System

A Laravel 9 Student Information System (SIS) for exam result transcription: departments, semesters,
courses, modules, grades, students, staff, and results. Lecturers enter CA/FE marks per module; students
view their results and transcripts.

On the `feature/blockchain-results` branch, student **results** are additionally anchored on a blockchain
so any later tampering with the database row can be detected — and, if it happens, the original values
can be recovered straight from the chain. See [Blockchain Result Anchoring](#blockchain-result-anchoring)
below.

## Stack

- Laravel 9 / PHP 8, Livewire 2 for the interactive results/grade-entry views
- Blade + Bootstrap 5 (via `laravel/ui`) for the UI
- SQLite (simplest for local dev) or MySQL — either works via `.env`
- Blockchain layer (this branch only): Hardhat + Solidity, `web3p/web3.php` as the PHP↔chain bridge

## Setup & Run (the Laravel app)

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` — simplest local option is SQLite:

```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/transcript/database/database.sqlite
```

```bash
touch database/database.sqlite   # only if using sqlite and the file doesn't exist yet
php artisan migrate --seed
npm run dev      # compiles Bootstrap/Vite assets — leave running, or `npm run build` once
php artisan serve
```

Visit `http://127.0.0.1:8000/login`. Seeded staff accounts use the password `12345` (see
`database/seeders/data/institution.php` for the seeded email list); seeded students use their
registration number-based credentials from `StudentSeeder`.

## Blockchain Result Anchoring

### What it does

Every time a lecturer saves a student's CA/FE marks (`StudentsResultsList::saveStudentResults()`), the
app also anchors that result on a blockchain via a small smart contract, `ResultLedger.sol`. Anchoring
means: build a fixed-order snapshot of the result's fields
(`student_reg_no|module_code|c_a_marks|f_e_marks|total_marks|grade_id`) and store that snapshot on-chain,
keyed by the result's database ID.

Later, clicking **Verify** on a result recomputes the same snapshot from whatever is *currently* in the
`results` table and asks the contract whether it still matches what was anchored:

- **✔ Matches chain** — the database row hasn't changed since it was anchored.
- **⚠ Tampered** — the database row no longer matches. Because the contract stores the full original
  snapshot (not just a hash of it), the app also reads that original snapshot back from the chain and
  displays it (*"Originally: CA 32, FE 60, Total 92, Grade A"*) — so a tampered row doesn't just get
  flagged, the original values are still recoverable.

### Why store the full snapshot instead of just a hash?

A hash alone can *detect* tampering but can't reveal the original values (hashing is one-way). Storing
the full snapshot lets the chain double as the recovery source, without needing a separate off-chain
audit-log table — which wouldn't itself be protected against the same tampering it's meant to catch.

**Trade-off:** this means result data (marks, keyed by registration number — no names) is readable by
anyone who can query the deployed contract. That's a non-issue on the local-only Hardhat network this
branch targets, but it's a real consideration before ever pointing this pattern at a public network —
sensitive fields would need hashing/encryption first, or a permissioned chain, in a production setting.

### Running the blockchain layer locally

The contract lives in `blockchain/` as its own small Hardhat project (separate from the Laravel app):

```bash
cd blockchain
npm install                                          # first time only
npx hardhat node                                      # terminal 1 — keep running
npx hardhat run scripts/deploy.js --network localhost # terminal 2, right after the node starts
```

The Hardhat node keeps its chain **in memory only** — restarting it wipes every previously anchored
result. As long as the deploy script is the *first* transaction run against a fresh node, the contract
lands at the same deterministic address already set in `.env`
(`0x5FbDB2315678afecb367f032d93F642f64180aa3`, Hardhat's Account #0). If some other transaction runs
first, the address will differ and `.env`'s `RESULT_LEDGER_ADDRESS` needs updating to match whatever
`blockchain/deployments/localhost.json` reports.

Run the contract's own test suite with:

```bash
cd blockchain
npx hardhat test
```

### Required `.env` entries

```
BLOCKCHAIN_RPC_URL=http://127.0.0.1:8545
RESULT_LEDGER_ADDRESS=0x5FbDB2315678afecb367f032d93F642f64180aa3
RESULT_LEDGER_ANCHOR_ADDRESS=0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266
```

`RESULT_LEDGER_ANCHOR_ADDRESS` is Hardhat's local Account #0 — the Hardhat node holds that account's
private key itself and signs on its behalf, so no private key ever needs to be configured in Laravel.
Only ever point this at local Hardhat test accounts, never a real wallet.

### Code map

- `blockchain/contracts/ResultLedger.sol` — the contract (`anchorResult`, `verifyResult`,
  `getAnchoredData`)
- `blockchain/test/ResultLedger.js` — Hardhat/Mocha tests for the contract
- `app/Services/BlockchainAnchorService.php` — the Laravel↔chain bridge (via `web3p/web3.php`)
- `app/Http/Livewire/Lecturer/StudentsResultsList.php` — hooks anchoring into
  `saveStudentResults()`, exposes `verifyOnBlockchain()`
- `resources/views/livewire/lecturer/students-results-list.blade.php` — the "Blockchain" column UI
- `database/migrations/..._add_blockchain_columns_to_results_table.php` — adds `anchored_hash` and
  `anchor_tx_hash` to `results`

---

Built with [Laravel](https://laravel.com).
