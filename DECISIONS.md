# Architecture & Technical Decisions (DECISIONS.md)

**Project:** AI Builds My Website  
**Candidate Submission for:** Full Stack Development Internship at Websites.co.in  
**Stack:** Laravel 11, MySQL, Google Gemini 1.5 Flash / Pro, Tailwind CSS  

---

## 1. Key Architectural Choices

### A. Framework & Database Model (Laravel 11 + MySQL)
- **Hybrid Schema Design:**  
  Businesses have both structured relational attributes (name, city, phone, email, status) and flexible, evolving attributes (hours, social links, section content).  
  - Relational tables (`businesses`, `sites`, `inquiries`, `data_conflicts`, `media_files`) guarantee referential integrity and performant querying.
  - JSON columns (`site_sections.content`, `extracted_data_sources.parsed_facts`, `sites.seo_metadata`) store section-specific payloads (e.g., custom menu items, pricing matrices, team members). This prevents rigid schema migrations whenever new section types are introduced.
- **Section-Driven Architecture:**  
  Websites are modeled as ordered collections of independent `SiteSection` models. This enables:
  - Granular, independent re-generation of individual sections without wiping out the entire page.
  - Per-section visibility toggling.
  - Direct JSON editing without complex DOM parsing.

### B. AI Engine: Google Gemini 1.5 Flash
- **Why Gemini 1.5 Flash:**
  - **Generous Free Tier:** Enables zero-cost local evaluation and development without needing corporate credentials.
  - **Huge Context Window (1M Tokens):** Easily ingests lengthy PDF brochures, 50-page restaurant menus, and multi-source raw web pages in a single turn without truncating critical context.
  - **Structured JSON Mode:** Leveraged `responseMimeType: "application/json"` combined with strict system instructions to guarantee machine-readable JSON output every time.
  - **Low Latency:** 2–3x faster response times compared to heavier models, delivering a snappier user experience.

### C. The "Golden Rule" Enforcement (Never Invent Facts)
Hallucination is the fatal flaw of AI website builders. We enforced truthfulness at multiple levels:
1. **Strict System Instructions:**  
   The AI prompt explicitly forbids guessing or generating synthetic phone numbers, emails, addresses, or prices.
2. **Flagging Gaps:**  
   If an attribute is not present in the ingested data, the AI is instructed to return `null` and flag `needs_user_input: true`.
3. **Editor Highlights:**  
   Unverified or missing data points are rendered with visual badges in the editor, prompting the business owner to confirm or supply them.

### D. Edge Cases & Defensive Engineering

| Edge Case | Solution Implemented |
|---|---|
| **Multiple businesses with same name** | `DisambiguationService`: Evaluates city, category, and address similarity scoring. If a city diverges or multiple high-confidence matches are found, it flags ambiguity for user confirmation. |
| **Sources that disagree (e.g., two phone numbers)** | `ConflictDetectorService`: Normalizes phone digits (handling `+91`, spaces, formatting) and computes string similarity on addresses. Genuine discrepancies are logged in `data_conflicts` table and rendered as an interactive resolution banner in the editor. |
| **Social sites blocking scraping** | `WebCrawlerService`: Catches HTTP 403, 429, and 999 bot blocks gracefully. It attempts OpenGraph metadata fallback and tags the source as `blocked_anti_scraping`, prompting the user for fallback details rather than crashing the request. |
| **Indirect Prompt Injection in uploaded files** | `PromptInjectionGuard`: Pre-processes uploaded PDFs/text files, redacts known injection triggers (e.g., `"ignore previous instructions"`, `"you are now DAN"`), bounds text length, and isolates raw data inside XML `<user_provided_data is_untrusted="true">` delimiters. |
| **AI Downtime or Rate Limiting (HTTP 429/500/503)** | `TemplateFallbackGenerator`: Implements exponential backoff retries. If the Gemini API remains unreachable or unconfigured, the app seamlessly falls back to a deterministic, high-quality rule-based generator that uses extracted facts. |

---

## 2. What Was Cut (and Why)

1. **Free-Form Drag-and-Drop Canvas:**  
   - *Why cut:* Free-form canvas editors (like Elementor or Webflow) introduce severe CSS layout bugs on mobile devices and steep learning curves for non-technical small business owners.  
   - *Alternative chosen:* Modular, bulletproof section blocks with instant theme switching (Modern, Bold, Warm Earthy, Elegant, Tech) and live preview.
2. **Automated Domain Registrar API (DNS / WHOIS):**  
   - *Why cut:* Out of scope for assignment timeframe and requires paid third-party API credentials (GoDaddy/Namecheap).  
   - *Alternative chosen:* Immediate slug-based public publishing at `/site/{slug}` with instant live/draft toggling.
3. **Heavy Client-Side Framework (React/Vue SPA Setup):**  
   - *Why cut:* Added unnecessary build complexity, Node compilation overhead, and hydration delays.  
   - *Alternative chosen:* Clean Blade templates powered by Tailwind CSS and reactive vanilla JavaScript for lightning-fast server-rendered performance and instant visual preview.

---

## 3. What's Next (Production Roadmap)

1. **Custom Domains with Automated SSL (Caddy / Cloudflare for SaaS):**  
   Allow business owners to map `theirbusiness.com` with zero-touch SSL certificate generation.
2. **Instant WhatsApp Business Lead Webhooks:**  
   Route inquiries submitted on the public website directly to the owner's WhatsApp number in real-time.
3. **Multi-Lingual Auto-Translation:**  
   Provide 1-click site translation into major Indian regional languages (Hindi, Marathi, Gujarati, Tamil, Bengali) while preserving business terms.
4. **Lightweight E-Commerce & Razorpay Checkout:**  
   Enable service bookings and product sales directly from the generated catalog with UPI payments.
