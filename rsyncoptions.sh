#!/usr/bin/env bash

  # Get Key out of keychain
  idrsa=$(security 2>&1 >/dev/null find-generic-password -gs IDRSA | cut -d '"' -f 2|sed s/\\\\012/\\\\n/g)
  echo -e "$idrsa" > "$hidrive_ssh_key_file"; chmod 600 "$hidrive_ssh_key_file"
  rsyncoptions=( --checksum --delete-after --delete-excluded --exclude "*.DS_Store" --exclude `printf '*'Icon'\r'` )
  # Sync
  rsync -avz -e "ssh -i $hidrive_ssh_key_file -l username"\
          "${rsyncoptions[@]}"              "$source_folder"   "$target_folder"  #> /dev/null 2>&1