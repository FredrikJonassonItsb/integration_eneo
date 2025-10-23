# Quick Start Guide - Eneo AI Integration

Kom igång med Eneo AI Integration för Nextcloud på 10 minuter!

## Förutsättningar

- ✅ Nextcloud 26+ installerat
- ✅ Eneo AI-plattform igång
- ✅ Root/sudo-åtkomst till servern
- ✅ Node.js 16+ installerat

## Steg 1: Installera Appen (2 min)

```bash
# Navigera till Nextcloud apps-katalog
cd /var/www/nextcloud/apps

# Klona repository
git clone https://github.com/FredrikJonassonItsb/ITSLyzer.git integration_eneo

# Gå in i katalogen
cd integration_eneo

# Installera beroenden
npm install

# Bygg frontend-assets
npm run build

# Sätt korrekta rättigheter
chown -R www-data:www-data /var/www/nextcloud/apps/integration_eneo
```

## Steg 2: Aktivera Appen (1 min)

```bash
# Aktivera appen via kommandoraden
sudo -u www-data php /var/www/nextcloud/occ app:enable integration_eneo

# Verifiera att appen är aktiverad
sudo -u www-data php /var/www/nextcloud/occ app:list | grep integration_eneo
```

**Alternativ:** Aktivera via webbgränssnittet:
1. Logga in som admin
2. Gå till **Apps**
3. Sök efter "Eneo"
4. Klicka **Enable**

## Steg 3: Konfigurera OAuth2 i Eneo (3 min)

1. Logga in på din Eneo-instans som admin
2. Gå till **Settings** → **OAuth2 Applications**
3. Klicka **New Application**
4. Fyll i:
   - **Name**: Nextcloud Integration
   - **Redirect URI**: `https://din-nextcloud.se/apps/integration_eneo/oauth/callback`
   - **Scopes**: `read`, `write`, `files`
5. Klicka **Save**
6. Kopiera **Client ID** och **Client Secret**

## Steg 4: Konfigurera Nextcloud (2 min)

1. Logga in på Nextcloud som admin
2. Gå till **Settings** → **Administration** → **AI**
3. Hitta **Eneo AI Integration**
4. Fyll i:
   - **Eneo API URL**: `https://din-eneo.se` (eller `http://localhost:8000`)
   - **OAuth2 Client ID**: (från steg 3)
   - **OAuth2 Client Secret**: (från steg 3)
   - ✅ **Enable Eneo integration**
5. Klicka **Save**
6. Klicka **Test Connection** → Ska visa "Connection successful"

## Steg 5: Anslut Användarkonto (2 min)

1. Logga in som vanlig användare
2. Gå till **Settings** → **Personal** → **AI**
3. Hitta **Eneo AI Assistant**
4. ✅ Kryssa i **Enable Eneo AI assistant**
5. Klicka **Connect to Eneo**
6. Du omdirigeras till Eneo
7. Logga in (om inte redan inloggad)
8. Klicka **Authorize**
9. Du omdirigeras tillbaka till Nextcloud
10. Verifiera att status visar "Connected to Eneo" ✅

## Steg 6: Testa Integrationen (1 min)

### Test 1: Smart Picker

1. Öppna **Text**-appen
2. Skapa nytt dokument
3. Skriv `/` → Smart Picker öppnas
4. Välj **Eneo AI Assistant**
5. Skriv: "Vad är huvudstaden i Sverige?"
6. Klicka **Ask Eneo**
7. Vänta på svar → "Huvudstaden i Sverige är Stockholm." ✅

### Test 2: Filindexering

1. Öppna **Files**-appen
2. Skapa en textfil med lite innehåll
3. Högerklicka på filen
4. Välj **Index in Eneo** (om tillgängligt)
5. Vänta på indexering
6. Gå till **Settings** → **Personal** → **AI**
7. Verifiera att filen visas under "Indexed Files" ✅

## Klart! 🎉

Du har nu en fungerande Eneo AI-integration i Nextcloud!

## Nästa Steg

### För Användare
- Utforska Smart Picker i olika appar (Text, Talk, Mail)
- Indexera viktiga dokument för bättre AI-svar
- Experimentera med olika typer av frågor

### För Administratörer
- Konfigurera backup för OAuth-tokens
- Övervaka loggfiler för fel
- Planera för skalning vid behov
- Läs INSTALLATION.md för avancerad konfiguration

## Felsökning

### Problem: "Connection failed"

**Lösning:**
```bash
# Testa Eneo från Nextcloud-servern
curl http://din-eneo.se/api/v1/health

# Om det inte fungerar, kontrollera:
# 1. Är Eneo igång?
# 2. Är brandväggen öppen?
# 3. Är URL:en korrekt?
```

### Problem: "OAuth2 authorization failed"

**Lösning:**
1. Verifiera att Client ID och Secret är korrekta
2. Kontrollera att Redirect URI matchar exakt
3. Kolla att OAuth2-klienten är aktiverad i Eneo
4. Rensa webbläsarens cookies och försök igen

### Problem: "Smart Picker visar inte Eneo"

**Lösning:**
```bash
# Bygg om frontend-assets
cd /var/www/nextcloud/apps/integration_eneo
npm run build

# Rensa Nextcloud-cache
sudo -u www-data php /var/www/nextcloud/occ maintenance:repair

# Rensa webbläsarens cache (Ctrl+Shift+R)
```

### Problem: "File indexing failed"

**Lösning:**
1. Kontrollera filbehörigheter i Nextcloud
2. Verifiera att filtypen stöds av Eneo
3. Kolla Eneo-loggar för specifika fel
4. Se till att Eneo har tillräckligt diskutrymme

## Hjälp och Support

- 📖 **Dokumentation**: Se README.md, INSTALLATION.md, ARCHITECTURE.md
- 🐛 **Buggar**: https://github.com/FredrikJonassonItsb/ITSLyzer/issues
- 💬 **Community**: Nextcloud Help Forum
- 📧 **Kontakt**: Sundsvalls kommun IT

## Användningsexempel

### Exempel 1: Sammanfatta Dokument

1. Indexera ett långt dokument
2. Öppna Smart Picker
3. Fråga: "Sammanfatta dokumentet i 3 punkter"
4. Eneo analyserar och ger sammanfattning

### Exempel 2: Besvara Frågor från Möte

1. Indexera mötesanteckningar
2. I Text-dokument, öppna Smart Picker
3. Fråga: "Vilka beslut togs på mötet?"
4. Eneo hittar och listar besluten

### Exempel 3: Generera Text

1. Öppna Smart Picker
2. Fråga: "Skriv ett mejl om projektuppdatering"
3. Eneo genererar ett utkast
4. Kopiera och anpassa efter behov

## Säkerhetstips

- 🔒 Använd alltid HTTPS i produktion
- 🔑 Rotera OAuth2-secrets regelbundet
- 👥 Aktivera 2FA för admin-konton
- 📊 Övervaka loggar för misstänkt aktivitet
- 🔄 Håll programvara uppdaterad

## Prestandatips

- ⚡ Använd Redis för caching
- 💾 Öka PHP memory_limit till 512MB
- 🔧 Konfigurera background jobs för indexering
- 📈 Övervaka resurser och skala vid behov

## Etiska Riktlinjer

När du använder AI i offentlig sektor:

- ✅ Var transparent om AI-användning
- ✅ Respektera användarnas integritet
- ✅ Granska AI-svar kritiskt
- ✅ Använd lokala modeller när möjligt
- ✅ Dokumentera AI-beslut

## Licens och Öppenhet

- **Licens**: AGPL-3.0-or-later
- **Öppen källkod**: Fullständig transparens
- **Ingen vendor lock-in**: Kör på egen infrastruktur
- **Community-driven**: Bidrag välkomna

## Versionsinfo

- **Version**: 1.0.0
- **Release Date**: 2025-01-23
- **Status**: Production Ready
- **Nextcloud**: 26-32
- **PHP**: 8.0+
- **Node.js**: 16+

---

**Lycka till med din Eneo AI-integration!** 🚀

Om du har frågor eller feedback, tveka inte att öppna ett issue på GitHub eller kontakta oss.

*Utvecklat av Sundsvalls kommun för Hubs-plattformen*

