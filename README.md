TarantoBot è un sistema automatico di riuso dei dati della Protezione Civile Puglia
In questa prima versione:
- Meteo
- Previsioni meteo
- Bollettini Protezione Civile Puglia 
insieme a quelli dei cittadini inseriti tramite il portale www.lecce-events.it e presenti sul portale opendata di Lecce
- Temperatura dell'aria
- Elenco Farmacie presenti sul portale opendata di Lecce e riversate su openStreetMap dalla comunità 


Uso:
- Cercare su Telegram l'utente "tarantobot" e fare Avvia
- Inviare una segnalazione cliccando "posizione" dal menù a forma di graffetta e dopo alcuni secondi verrà chiesto di inviare il contenuto della segnalazione piuttosto che digitare "farmacie".
- Alternativamente si possono cliccare le etichette già predefinite (meteo, eventi, ect)
- Le segnalazioni sono poi visibili su una mappa Umap


Installazione:
- Impostare rinominando setting.php e setting_t.php con il numero di risultati della ricerca openstreetmap e il raggio d'azione. Inserire il Token ricevuto per l'attivazione del Bot, inserire l'API Key del servizio google shortner per l'abbraviazione dei links.
- Attivare o un crontab php start.php getupdates oppure attivare SSL , inserire in settings.php il link https al file start.php e lanciare "php start.php sethook"



Progetto derivato da @emergenzeprato, LIC MIT di Matteo Tempestini http://iltempe.github.io/Emergenzeprato
