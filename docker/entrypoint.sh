#!/bin/sh
readonly DATABASE="/database/exambox.db"
readonly SQL_DATABASE_INIT="/app/database/database.sql"

toupper() {
  echo "$1" | awk '{ print toupper($0) }'
}

log_header() {

  echo "
==================================================================
      $(toupper "$1")
=================================================================="
}

hangout() {
  log_header 'UPMATH'
}

main() {
  hangout
  printenv > /etc/environment
  exec /usr/bin/supervisord -c /etc/superv.conf
}

main
