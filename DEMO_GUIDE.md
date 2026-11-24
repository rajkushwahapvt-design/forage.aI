# 5-Minute Demo Video Guide & Presentation Script

**Project:** AI Builds My Website (Websites.co.in Assignment)  
**Demo Video Target Duration:** ~5 minutes  
**Goal:** Showcase building high-quality, ready-to-publish websites for **2 real businesses** while demonstrating edge-case handling, section regeneration, and live publishing.

---

## Real Businesses Chosen for the Demo

### Business 1: "Sharma & Sons Artisan Bakery"
- **Location:** Bandra West, Mumbai, Maharashtra
- **Category:** Artisan Bakery & Specialty Cafe
- **Inputs Supplied:**
  - Name: `Sharma & Sons Artisan Bakery`
  - City: `Bandra West, Mumbai`
  - Phone: `+91 98201 54321`
  - Email: `orders@sharmabakerymumbai.com`
  - Google Business URL: `https://maps.google.com/?cid=123456789`
  - Instagram: `https://instagram.com/sharma_artisan_bakery`
  - Uploaded File: Sample menu/price list (Sourdough Loaf ₹280, Croissant Box ₹450, Belgian Chocolate Cake ₹950)
- **Theme:** Warm Earthy (Bakery/Food palette)

### Business 2: "Apex Plumbing & HVAC Solutions"
- **Location:** Indiranagar, Bangalore, Karnataka
- **Category:** Plumbing, Electrical & AC Repair Services
- **Inputs Supplied:**
  - Name: `Apex Plumbing & HVAC Solutions`
  - City: `Indiranagar, Bangalore`
  - Phone: `+91 80 4123 9876`
  - Email: `service@apexplumbhvac.in`
  - Google Business URL: `https://maps.google.com/?cid=987654321`
  - LinkedIn: `https://linkedin.com/company/apex-plumbing-hvac`
  - Edge Case Triggered: Disagreeing phone number between Google listing (`+91 80 4123 9876`) and brochure (`+91 98801 12345`).
- **Theme:** Bold Contractor (Sky Blue/Navy)

---

## Step-by-Step Video Walkthrough Script

### [0:00 - 0:45] Introduction & Problem Statement
> *"Hello! This is my submission for the Websites.co.in Full Stack Development internship assignment: 'AI Builds My Website'.  
> The core problem we are solving is helping local business owners convert whatever scattered digital assets they have—a name, social links, Google listing, or price lists—into a complete, ready-to-publish website with zero hallucinated facts."*
- **Action:** Show the clean landing page at `http://localhost:8000`. Point out the intake wizard, the built-in edge-case protection indicators, and the 1-click test presets.

### [0:45 - 2:15] Demo 1: Sharma & Sons Artisan Bakery (Mumbai)
1. **Action:** Click the preset button **"Real Business 1: Bakery (Mumbai)"**.
2. **Narration:**  
   > *"Let's build a website for our first real business: Sharma & Sons Artisan Bakery in Bandra West, Mumbai. Notice we have provided their name, city, phone, email, Google Maps link, and Instagram profile, plus an uploaded price list."*
3. **Action:** Click **"Build My Website Now"**.
4. **Narration:**  
   > *"The app crawls the links, parses the uploaded price list, screens for prompt injection, and invokes the AI engine with our strict Golden Rule: never invent facts."*
5. **Action:** Inside the Editor, showcase the generated site in the preview pane:
   - Header with click-to-call phone number
   - Hero banner with tagline tailored to Mumbai
   - About Us highlighting artisanal craftsmanship
   - Menu items with exact prices extracted from the price list (₹280, ₹450, ₹950)
   - Photo showcase and customer reviews
   - Working contact and inquiry form
6. **Action:** Click **"Regenerate"** on the Hero section, enter prompt: `"Make the headline warmer and emphasize fresh morning sourdough"`, and click **Regenerate with AI**. Show the instant live preview update!

### [2:15 - 3:45] Demo 2: Apex Plumbing & HVAC Solutions & Edge Cases
1. **Action:** Return to `/` and click **"Real Business 2: HVAC & Plumbing (Bangalore)"**.
2. **Narration:**  
   > *"Now let's test our second real business: Apex Plumbing & HVAC Solutions in Bangalore. Here, we'll demonstrate how our app handles crucial real-world edge cases."*
3. **Action:** Click **"Build My Website Now"**.
4. **Narration:**  
   > *"Notice the red notification banner at the top of the editor: 'Conflicting Facts Detected Between Sources'. Our ConflictDetectorService caught a discrepancy: the intake form has one phone number, while the crawled document has another. Rather than hallucinating or picking randomly, we follow the Golden Rule: ask the user. With one click, the owner selects which phone number to keep."*
5. **Action:** Click to resolve the conflict. Notice the banner clears and the site updates immediately.
6. **Action:** Switch the theme dropdown to **"Bold Contractor"** and demonstrate the instant color palette shift.

### [3:45 - 4:30] Public Publishing & Lead Capture
1. **Action:** Point out the slug `/site/apex-plumbing-hvac-solutions-indiranagar`.
2. **Action:** Click **"Publish to Public URL"**. The status badge turns green (*Published*).
3. **Action:** Click **"Preview Site"** to open the public site in a new tab.
4. **Narration:**  
   > *"The website is now live at its public URL. Let's test the lead capture inquiry form."*
5. **Action:** Scroll to the bottom contact form, enter `Name: Rahul Mehta`, `Phone: 9876543210`, `Message: Need emergency pipe repair today`, and submit.
6. **Action:** Show the green success message and mention that the inquiry is saved directly into the MySQL `inquiries` table.

### [4:30 - 5:00] Technical Wrap-Up & Closing
> *"Under the hood, this is powered by Laravel 11, MySQL, and Google Gemini 1.5 Flash. We've included comprehensive tests, clean architecture, prompt injection defenses, anti-scraping fallbacks, and a complete DECISIONS.md document. Thank you, and I look forward to discussing this further with the Websites.co.in team!"*
