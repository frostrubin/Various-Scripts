Eine Sammlung von Scripten zum direkten Lauf auf einer FRITZ!Box.     
Bei eingehenden Anrufen werden Push-Notifications versendet.

**Achtung! Der aktuelle Stand funktioniert nicht korrekt! Telnet Verbindung muss geöffnet bleiben damit das Script funktioniert.**

## Telnetd aktivieren
- Anschalten: `#96*7*`
- Abschalten: `#96*8*`

## Callmonitor aktivieren
Die FRITZ!Box kann auf Port 1012 einen TCP-Dienst anbieten, der über Anrufaktivitäten informiert.     
Wenn der Dienst eingeschaltet ist, kann man von der Box selbst per `nc localhost 1012` direkt auf Aktivitäten lauschen.
- Anschalten: `#96*5*`
- Abschalten: `#96*4*` 

## Scripte auf die FRITZ!Box bringen
1. Per `telnet fritz.box` verbinden. Password ist das des Web-Logins.
2. Per `cd /var/tmp` das Verzeichnis wechseln.
3. Per `wget http://bitnugget.de/fritzbox/download.sh` und anschließendes `chmod +x download.sh` das Master-Script herunterladen.
4. Per `./download.sh` das Script ausführen.
5. Per `echo youremailaddress@example.com > /var/flash/iPadNotify/emails.txt` die Daten hinterlegen
6. Per `/var/flash/iPadNotify/notify.sh &` das Script starten.

## Benutzer beim Boxcar Dienst hinzufügen
1. Boxcar App Runterladen und Account anlegen
2. Per `curl -d "email=youremailaddress@example.com" http://boxcar.io/devices/providers/MH0S7xOFSwVLNvNhTpiC/notifications/subscribe` den _Monitoring_ Dienst subscriben.

## Session beenden
- Per `Ctrl` + `]` die Telnet Befehlszeile hochbringen
- Per `quit` beenden


## Quellen
- [http://www.wehavemorefun.de/fritzbox/Telnetd](http://www.wehavemorefun.de/fritzbox/Telnetd)
- [http://www.wehavemorefun.de/fritzbox/Starten_von_telnetd](http://www.wehavemorefun.de/fritzbox/Starten_von_telnetd)
- [http://www.wehavemorefun.de/fritzbox/Callmonitor](http://www.wehavemorefun.de/fritzbox/Callmonitor)
- [http://www.wehavemorefun.de/fritzbox/Anrufmonitor_nutzen](http://www.wehavemorefun.de/fritzbox/Anrufmonitor_nutzen)
- [http://www.wehavemorefun.de/fritzbox/Phonebook](http://www.wehavemorefun.de/fritzbox/Phonebook)
- [http://lancethepants.com/files/index.php?dir=Binaries/curl/curl%20%28openssl%29/](http://lancethepants.com/files/index.php?dir=Binaries/curl/curl%20%28openssl%29/)