# Email Submission Template

**To:** hr@websites.co.in  
**Subject:** Full Stack Development Internship Assignment Submission - AI Builds My Website - [Your Name]  

---

Dear Websites.co.in HR & Engineering Team,

Thank you for the opportunity to apply for the Full Stack Development Internship at Websites.co.in.

I have completed the "AI Builds My Website" assignment using Laravel 11, MySQL, and Google Gemini AI. The project strictly enforces the **Golden Rule** (never invent facts, ask the user when unsure) and comprehensively handles all specified edge cases.

---

### Key Submission Links:
- **GitHub Repository:** `https://github.com/[YOUR_GITHUB_USERNAME]/ai-builds-my-website`
- **5-Minute Demo Video:** `https://youtu.be/[YOUR_DEMO_VIDEO_LINK]` *(Building sites for Sharma & Sons Artisan Bakery and Apex Plumbing & HVAC Solutions)*
- **DECISIONS.md:** Available in the repo root detailing key choices, scope boundaries, and the production roadmap.

---

### What Has Been Built:
1. **Multi-Source Ingestion & Crawler:** Ingests business name & city, Google Business listings, social media links, PDF menus/brochures, CSV price lists, and photos.
2. **Edge Case Defenses:**
   - **Disambiguation:** Disambiguates businesses sharing identical names via city and category confidence scoring.
   - **Source Conflict Detection:** Detects discrepancies between sources (e.g., differing phone numbers or addresses) and displays an interactive resolution UI.
   - **Anti-Scraping Resilience:** Gracefully handles HTTP 403/429/999 blocks from social media platforms with OpenGraph fallbacks.
   - **Indirect Prompt Injection Defense:** Sanitizes uploaded files against prompt injection vectors and strictly isolates untrusted text in XML delimiters.
   - **AI Downtime Resilience:** Features an automatic `TemplateFallbackGenerator` so users experience zero downtime if the AI API is rate-limited or unreachable.
3. **Live Section Editor & Publishing:** Lets owners customize styles, edit content directly, regenerate individual sections via AI prompts, and publish to public URLs (`/site/{slug}`) with MySQL lead capture.

---

### Internship Details:
- **Expected Stipend:** ₹[Insert Expected Amount, e.g. 15,000 - 25,000] / month
- **Earliest Joining Date:** [Insert Date, e.g. Immediately / 1 December 2026]

Looking forward to hearing from you and discussing this submission in the interview!

Warm regards,  
**[Your Full Name]**  
[Your Phone Number]  
[Your LinkedIn Profile]  
[Your GitHub Profile]  
