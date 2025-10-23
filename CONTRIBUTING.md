# Contributing to Eneo AI Integration

Tack för ditt intresse att bidra till Eneo AI Integration för Nextcloud! Vi välkomnar bidrag från alla.

## Hur du kan bidra

### Rapportera Buggar

Om du hittar en bugg, öppna ett issue på GitHub med:

1. **Beskrivning** av problemet
2. **Steg för att återskapa** buggen
3. **Förväntat beteende** vs faktiskt beteende
4. **Miljöinformation**:
   - Nextcloud-version
   - PHP-version
   - Webbläsare och version
   - Eneo-version
5. **Loggar** (om tillgängliga)
6. **Skärmdumpar** (om relevant)

### Föreslå Funktioner

Öppna ett issue med:

1. **Beskrivning** av funktionen
2. **Användningsfall** - varför behövs den?
3. **Förslag på implementation** (om du har idéer)
4. **Alternativ** du har övervägt

### Bidra med Kod

1. **Forka** repositoryt
2. **Skapa en branch** för din funktion:
   ```bash
   git checkout -b feature/min-nya-funktion
   ```
3. **Gör dina ändringar**
4. **Testa** dina ändringar:
   ```bash
   npm run lint
   npm test
   npm run build
   ```
5. **Commit** med beskrivande meddelanden:
   ```bash
   git commit -m "feat: Lägg till streaming-svar för realtidsupplevelse"
   ```
6. **Push** till din fork:
   ```bash
   git push origin feature/min-nya-funktion
   ```
7. **Öppna en Pull Request** på GitHub

## Commit-meddelanden

Vi följer [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - Ny funktion
- `fix:` - Buggfix
- `docs:` - Dokumentationsändringar
- `style:` - Formatering, saknade semikolon, etc.
- `refactor:` - Kodrefaktorering
- `test:` - Lägga till eller ändra tester
- `chore:` - Underhåll, dependencies, etc.

**Exempel:**
```
feat: Lägg till stöd för PDF-filindexering

Implementerar PDF-parsing med pdf2text och indexering i Eneo.
Stöder både text-baserade och OCR-skannade PDFs.

Closes #42
```

## Kodstil

### PHP

Följ [Nextcloud Coding Standards](https://docs.nextcloud.com/server/latest/developer_manual/getting_started/codingguidelines.html):

- PSR-12 för kodformatering
- Använd type hints
- Dokumentera med PHPDoc
- Använd dependency injection

**Exempel:**
```php
<?php

/**
 * Send a chat message to Eneo
 *
 * @param string $userId The user ID
 * @param string $message The message to send
 * @return array The response from Eneo
 * @throws Exception If the request fails
 */
public function sendChatMessage(string $userId, string $message): array {
    // Implementation
}
```

### JavaScript

Följ [Nextcloud JavaScript Guidelines](https://docs.nextcloud.com/server/latest/developer_manual/getting_started/codingguidelines.html#javascript):

- ESLint för linting
- Använd ES6+ syntax
- Dokumentera med JSDoc
- Använd async/await för asynkron kod

**Exempel:**
```javascript
/**
 * Send a chat message to Eneo
 * @param {string} message - The message to send
 * @param {object} context - Optional context
 * @returns {Promise<object>} The response from Eneo
 */
async function sendChatMessage(message, context = {}) {
    // Implementation
}
```

## Testning

### Manuella Tester

Kör igenom relevanta testfall i `TESTING.md` innan du skickar en PR.

### Automatiserade Tester

```bash
# PHP-tester (när implementerade)
composer test

# JavaScript-tester (när implementerade)
npm test

# Linting
npm run lint
npm run lint:fix
```

### Bygga

```bash
# Bygg frontend-assets
npm run build

# Utvecklingsläge med watch
npm run dev
```

## Pull Request Process

1. **Uppdatera dokumentation** om nödvändigt
2. **Lägg till tester** för nya funktioner
3. **Kör alla tester** och verifiera att de passerar
4. **Uppdatera CHANGELOG** (om det finns en)
5. **Beskriv dina ändringar** i PR-beskrivningen
6. **Länka till relaterade issues** med "Closes #123"
7. **Vänta på code review** från maintainers

### PR Checklist

- [ ] Koden följer projektets kodstil
- [ ] Jag har testat mina ändringar
- [ ] Jag har uppdaterat dokumentationen
- [ ] Jag har lagt till tester för nya funktioner
- [ ] Alla tester passerar
- [ ] Commit-meddelanden följer Conventional Commits
- [ ] PR-beskrivningen är tydlig och komplett

## Code Review

Alla PRs kommer att granskas av maintainers. Vi kan be om ändringar eller förtydliganden.

**Var tålmodig** - vi granskar PRs så snart vi kan, men det kan ta lite tid.

**Var öppen för feedback** - code review är till för att förbättra koden, inte kritisera dig.

## Säkerhet

Om du hittar en säkerhetsbrist, **öppna INTE ett publikt issue**. Istället:

1. Skicka ett email till säkerhetsansvarig (se README.md)
2. Beskriv sårbarheten i detalj
3. Ge oss tid att fixa innan du publicerar

## Licens

Genom att bidra accepterar du att dina bidrag licensieras under AGPL-3.0-or-later, samma som resten av projektet.

## Community Guidelines

- **Var respektfull** - alla är välkomna oavsett bakgrund
- **Var konstruktiv** - fokusera på lösningar, inte problem
- **Var tålmodig** - vi är alla här för att lära och förbättra
- **Var öppen** - dela dina idéer och lyssna på andras

## Frågor?

Om du har frågor om att bidra:

- Öppna ett issue med etiketten "question"
- Fråga i Nextcloud Community Forum
- Kontakta maintainers direkt

## Tack!

Tack för att du bidrar till Eneo AI Integration! Ditt bidrag hjälper till att göra AI mer tillgängligt, säkert och öppet för alla.

---

**Utvecklat av Sundsvalls kommun för Hubs-plattformen**

Med fokus på öppenhet, säkerhet och integritet.

