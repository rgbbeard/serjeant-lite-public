base="$(dirname $(readlink -f $0))"

. "$base/config.sh"
. "$base/shutdown.sh"

appstart() {
  docker_compose
  xdg-open "http://localhost:10004/"
}

docker_compose() {
  docker compose -f $configfile up -d
}

id=$(docker ps -aq -f name=$appname)

if [ "$2" = "--build" ]; then
  docker compose -f $configfile build
fi

if [ -z "$id" ]; then
  # this ensures that every container belonging to this application
  # will be shut down at startup
  appstop $imagename
  appstart
else
  appstart
fi
