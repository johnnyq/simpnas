#!/bin/bash
echo Checking For Updates...
apt update; apt dist-upgrade -y
echo Installing additional required packages...
apt install samba smartmontools php-cgi cryptsetup git docker.io -y 
echo Allowing Root Login through SSH...
sed -i 's/prohibit-password/yes/' /etc/ssh/sshd_config
cd /
echo Downloading the latest simpnas from GIT repo....
git clone https://github.com/johnnyq/simpnas.git
echo Making backup of existing smb.conf, copying new smb.conf to /etc/samba
mv /etc/samba/smb.conf /etc/samba/smb.conf.ori
cp /simpnas/conf/smb.conf /etc/samba/
touch /etc/samba/shares.conf
mkdir /etc/samba/shares
systemctl restart smbd
echo Installing and enabling simpnas service at bootup
cp /simpnas/conf/simpnas.service /etc/systemd/system/
chmod 755 /etc/systemd/system/simpnas.service
systemctl enable simpnas
echo Starting SimpNAS Web UI...
systemctl start simpnas
echo All Setup! You can now access your system by visiting http://$HOSTNAME in your web browser!!