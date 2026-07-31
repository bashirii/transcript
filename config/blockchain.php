<?php

return [
    'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'http://127.0.0.1:8545'),
    'result_ledger_address' => env('RESULT_LEDGER_ADDRESS'),
    'anchor_address' => env('RESULT_LEDGER_ANCHOR_ADDRESS'),
    'deployment_file' => base_path('blockchain/deployments/localhost.json'),
];
