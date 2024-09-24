shell="zenity"

source ./echonl.sh
source ./empty.sh

function showMessage() {
  if [ $(empty $1) -eq 0 ]; then
    if [[ $1 -eq 1 ]]; then;
      shell="$shell --info"
    elif [[ $1 -eq 2 ]]; then;
      shell="$shell --error"
    else
      shell="$shell --notification"
    fi
  else
    echonl "Popup type needed"
    echonl "Types: 1 = info, 2 = error, 0 = simple notification"
    exit 2
  fi

  if [ $(empty $2) -eq 0 ]; then
    shell="$shell --text=$2"
  else
    echonl "Missing popup message"
    exit 4
  fi

  if [ $(empty $3) -eq 0 ]; then
    shell="$shell --ok-label=$3"
  else
    shell="$shell --ok-label=Ok"
  fi

  if [ $1 -eq 0 ]; then
    if [ $(empty $4) -eq 0 ]; then
        shell="$shell --cancel-label=$4"
    else
        shell="$shell --cancel-label=Cancel"
    fi
  fi

  eval $shell
}