#!/usr/bin/env bash

# You probably really don't want to read this
command -v php >/dev/null 2>&1 || {
  echo "#######################################"
  echo >&2 "PHP version >= 5.5.7 required. Aborting.";
  echo "#######################################"
  exit 1;
}

if [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
  if [[ -z $(php -m | grep zip) ]]; then
    echo "#######################################"
    echo >&2 "Installing PHP Zip module...";
    echo "#######################################"
    FUCK=$(php -v | sed 's/[[:alpha:]|(|[:space:]]//g' | awk -F- '{print $1}' | head -n 1 | cut -b1-3)
    sudo apt update && sudo apt install php$(echo $FUCK)-zip -y
  fi
fi

host="localhost"
port="9999"

echo "#######################################"
echo "Usage: $0 host port"
echo "Defaults: $host $port"
echo ""
echo "Note: running !localhost will trigger"
echo "authentication - see config.php"
echo "#######################################"
echo ""

if [ ! -z "$1" ]
then
  host=$1
fi

if [ ! -z "$2" ]
then
  port=$2
fi

php -S $host:$port
