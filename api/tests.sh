#!/bin/bash

output=$(vendor/bin/phpunit --configuration phpunit.dist.xml)
echo "$output"

if echo "$output" | grep -qE '^OK \([0-9]+ tests?, [0-9]+ assertions?\)'; then
    echo "✅ Tests passés avec succès"
    exit 0
else
    echo "❌ Tests échoués"
    exit 1
fi