#!/usr/bin/env bash
set -eo pipefail

# Create mount directory for service.
mkdir -p "$MNT_DIR"

if [ "$1" = 'apache2' ]; then


  echo -e "Check DB connection $DB_HOST:$DB_PORT"
  until [[ $(nc -z "$DB_HOST" "$DB_PORT" &> /dev/null; echo $?) == '0' ]]
  do
      echo -e "wait: $DB_HOST:$DB_PORT";
      sleep 5
  done
fi

exec "$@"