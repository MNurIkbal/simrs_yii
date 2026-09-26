#!/bin/sh


read -p "Masukan nama container Backend (web_sirs) :" container
container=${container:-web_sirs}


FILE_HOOKS="$(git rev-parse --git-dir)/hooks/pre-push"

echo "${dirGitProject}"

cp "$(git rev-parse --git-dir)/hooks/pre-push.sample" $FILE_HOOKS

cat <<END > $FILE_HOOKS
#!/bin/sh
#
# An example hook script to verify what is about to be committed.
# Called by "git commit" with no arguments.  The hook should
# exit with non-zero status after issuing an appropriate message if
# it wants to stop the commit.
#
# To enable this hook, rename this file to "pre-commit".

echo "  _____    _   _    _                  _   _     _     "
echo " |  __ \  ( ) | |  | |                | | | |   | |    "
echo " | |  | | |/  | |__| |   ___    __ _  | | | |_  | |__  "
echo " | |  | |     |  __  |  / _ \  / _' | | | | __| | '_ \ "
echo " | |__| |     | |  | | |  __/ | (_| | | | | |_  | | | |"
echo " |_____/      |_|  |_|  \___|  \__,_| |_|  \__| |_| |_|"
echo "                                                       "
echo "   "
echo 'Pre Push Event !!!'


docker exec -t $container bash -c "cd /var/www/sirs/backend/${PWD##*/} && ../vendor/bin/codecept run unit" 
if [ \$? -ne 0 ]; then
  echo -e "\e[1;31mUnit Test Gagal ! Gagal untuk push ke repository.\e[0m" >&2
  exit 1;
  else
  echo -e "Unit Test Berhasil di proses !" >&2
  exit 0;
fi
END

echo "done"
