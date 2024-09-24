appstop() {
  containers="$(docker ps -a --format '{{.Names}}' | grep "$1")"
  for c in $containers; do
    echo -e "Stopping container: $c\n"
    docker rm -f $c
  done
}