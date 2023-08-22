#!/usr/bin/env bash
set -eo pipefail

# Create mount directory for service.
mkdir -p "$MNT_DIR"

if [ "$1" = 'apache2-foreground' ]; then

  if [ -z "$IS_LOCAL" ]; then
    [[ -z "$FILE_STORE_IP_ADDRESS" ]] && { echo "Error: env FILE_STORE_IP_ADDRESS not found"; exit 1; }
    [[ -z "$FILE_SHARE_NAME" ]] && { echo "Error: env FILE_SHARE_NAME not found"; exit 1; }

    echo "Mounting Cloud Filestore."
    mount -o nolock "$FILE_STORE_IP_ADDRESS":/"$FILE_SHARE_NAME" "$MNT_DIR"
    echo "Mounting completed."
  fi

  echo -e "Check DB connection $DB_HOST:$DB_PORT"
  until [[ $(nc -z "$DB_HOST" "$DB_PORT" &> /dev/null; echo $?) == '0' ]]
  do
      echo -e "wait: $DB_HOST:$DB_PORT";
      sleep 5
  done
fi

exec "$@"