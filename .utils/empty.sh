function empty() {
  [ -n "$1" ] && echo 0 || echo 1;
}