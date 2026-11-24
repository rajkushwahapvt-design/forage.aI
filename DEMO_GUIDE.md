# 5-Minute Demo Video Speech Script

> **How to Record:** Open `http://localhost:8000` on your screen. Follow this timed script word-for-word while performing the clicks indicated in `[Action]`. Total duration: **5 minutes**.

---

### [0:00 – 0:45] Intro & Core Philosophy
* **[Action]:** Show homepage at `http://localhost:8000`. Hover over the input fields.
* **[Spoken]:**  
  > *"Hi team Websites.co.in! I’m Raj Kushwaha, and this is my submission for 'AI Builds My Website'.  
  > The goal is simple: a business owner shares whatever they have—names, social links, Google Maps, or price lists—and the app builds a production-ready website.  
  > Our core rule is the **Golden Rule: Never invent facts**. If an address or price isn't verified, we never hallucinate—we prompt the user. Let's see this in action for two real businesses."*

---

### [0:45 – 2:15] Real Business 1: Bakery (Mumbai) & Section Regeneration
* **[Action]:** Click preset button **"Real Business 1: Bakery (Mumbai)"**, then click **"Build My Website Now"**.
* **[Spoken]:**  
  > *"First, we test 'Sharma & Sons Artisan Bakery' in Bandra West, Mumbai, with an Instagram link, Google listing, and an uploaded price list.  
  > The backend crawls the links, parses the pricing, and passes verified facts to Gemini.  
  > In seconds, we get a complete website: header with click-to-call, localized hero banner, verified products with exact prices (Sourdough ₹280, Belgian Cake ₹950), customer reviews, and hours.  
  > Now let's test section regeneration. I'll click 'Regenerate' on the Hero section, ask the AI to 'emphasize fresh morning sourdough', and click Regenerate. As you can see, the hero section updates instantly while preserving the rest of the site."*

---

### [2:15 – 3:45] Real Business 2: HVAC (Bangalore) & Edge Cases
* **[Action]:** Return to home, click **"Real Business 2: HVAC & Plumbing (Bangalore)"**, and click **"Build My Website Now"**.
* **[Spoken]:**  
  > *"Now let's test our second business: 'Apex Plumbing & HVAC Solutions' in Bangalore, specifically looking at edge case handling.  
  > Notice the red alert bar at the top: 'Conflicting Facts Detected'. Our crawler found one phone number on Google Maps and a different one in their document.  
  > Instead of guessing, we ask the user. With one click, I select the verified number, and the conflict is resolved in MySQL.  
  > We also protect against indirect prompt injection in uploaded files by sandboxing untrusted content in XML delimiters, and we handle anti-scraping on social profiles gracefully without crashing."*

---

### [3:45 – 4:30] Live Publishing & Lead Capture
* **[Action]:** Click **"Publish to Public URL"**, then click **"Preview Site"**. Scroll to the bottom contact form, type a test name and message, and submit.
* **[Spoken]:**  
  > *"Once satisfied, the owner clicks 'Publish to Public URL'. The site is instantly live at `/site/apex-plumbing-hvac-solutions-indiranagar`.  
  > It is fully responsive across desktop, tablet, and mobile.  
  > At the bottom, visitors can submit inquiries. Let's submit a test lead. The submission succeeds immediately and is saved directly into the MySQL database for the owner to manage."*

---

### [4:30 – 5:00] Wrap-Up
* **[Action]:** Show the GitHub repo at `https://github.com/rajkushwahapvt-design/forage.aI`.
* **[Spoken]:**  
  > *"To summarize: Built with Laravel 11, MySQL, and Gemini 1.5 Flash, complete with a deterministic fallback generator for zero downtime. The repo includes full migrations, tests, and a DECISIONS.md file.  
  > Thank you, and I look forward to the interview!"*
