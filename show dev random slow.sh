while read -n1 char; do
#do something with the byte in $char
echo -ne "$char";
sleep 0.05;
done </dev/random