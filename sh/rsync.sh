echo "Connecting... dd"
rsync -avzh --exclude=".out" -e "ssh -p 20002" userid@0.0.0.0:/home/rsv/public_html/log*.out /home/rsv/logs