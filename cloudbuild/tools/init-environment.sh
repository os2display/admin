#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

function initialise_type {
  type=$1
curl -s "http://search:3010/api" -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type:application/json" -X POST --data @<(cat <<EOF
{
      "index":"${ADMIN_SEARCH_INDEX_ID}",
      "type":"$type",
      "id":1,
      "data": {
        "name": "test",
        "user": 1,
        "created_at": 0
      }
}
EOF
)

curl -s "http://search:3010/api" -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type:application/json" -X DELETE --data @<(cat <<EOF
{
      "index":"${ADMIN_SEARCH_INDEX_ID}",
      "type":"$type",
      "id":1
}
EOF
)
}

if [[ $# -lt 5 ]] ; then
    echo "Syntax: $0 <admin username> <admin email> <admin password> <search apikey> <admin search index id>"
    exit 1
fi

ADMIN_USER="$1"
ADMIN_EMAIL="$2"
ADMIN_PASS="$3"
SEARCH_APIKEY="$4"
ADMIN_SEARCH_INDEX_ID="$5"

gosu www-data bin/console doctrine:migrations:migrate --no-interaction
gosu www-data bin/console os2display:core:templates:load
gosu www-data bin/console doctrine:query:sql "UPDATE ik_screen_templates SET enabled=1;"
gosu www-data bin/console doctrine:query:sql "UPDATE ik_slide_templates SET enabled=1;"

bin/console fos:user:create "${ADMIN_USER}" "${ADMIN_EMAIL}" "${ADMIN_PASS}" --super-admin || true

# Authenticate to get a token we can use to subsequent call.
JSON_RESULT=$(curl -s "http://search:3010/authenticate" -H "Accept: application/json" -H "Content-Type:application/json" -X POST --data @<(cat <<EOF
{
   "apikey": "${SEARCH_APIKEY}"
  }
EOF
) 2>/dev/null)
TOKEN=$(echo "$JSON_RESULT"|php -r 'echo json_decode(fgets(STDIN))->token;')

# Activate the main index
curl -s "http://search:3010/api/${ADMIN_SEARCH_INDEX_ID}/activate" -H "Authorization: Bearer $TOKEN" 2>/dev/null

JSON_RESULT=$(curl -s "http://search:3010/authenticate" -H "Accept: application/json" -H "Content-Type:application/json" -X POST --data @<(cat <<EOF
{
   "apikey": "${SEARCH_APIKEY}"
  }
EOF
) 2>/dev/null)

TOKEN=$(echo "$JSON_RESULT"|php -r 'echo json_decode(fgets(STDIN))->token;')

arr="Os2Display\\\\CoreBundle\\\\Entity\\\\Slide
Os2Display\\\\CoreBundle\\\\Entity\\\\Channel
Os2Display\\\\CoreBundle\\\\Entity\\\\Screen
Os2Display\\\\MediaBundle\\\\Entity\\\\Media"

# Now go trough and initialize the elastisearch-mapping for each of our entity types.
for TYPE in $arr
do
(
    initialise_type $TYPE
)
done
