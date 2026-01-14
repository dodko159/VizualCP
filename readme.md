Inštalácia PHP závislosti: composer install --no-interaction

Inštalácia Vue časti web aplikácie:

1. v priečinku vue-project príkaz: **npm install**
2. Projekt bol vyvíjaný s verziou Node 22
3. DEV: **npm run dev**, PROD: **npm run build**

Nastavenia web aplikácie v súbore: **app-config.json**
Dostupné 3 profily: DEV, STAGE, PROD.

**dbFilename** - cesta k SQL3Lite databáze, momentálne používaná k ukladaniu čísla ponuky. Cesta ideálne mimo priečinku
so zdrojákmi, aby nedošlo k prepísaniu dát<br>
**isProductionSmtp** - ak je hodnota: **true**, použijú sa údaje z app-config.json, inak localhost<br>
**isProductionVue** - pre produkciu hodnota: **true**<br>
**prefix: mail** - nastavenia SMTP serveru<br>
**prefix: reCaptcha** - nastavenia Google ReCAPTCHA V2 Checkbox