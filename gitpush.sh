#!/bin/bash
# Get Github Password, use it to push, empty the clipboard

password=$(security 2>&1 >/dev/null find-generic-password -gs GitHubPushPassword | cut -d '"' -f 2)
echo "$password"|pbcopy;git push; echo "leer"|pbcopy; exit 0
