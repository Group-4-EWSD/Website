<script setup lang="ts">
import { Button } from '@/components/ui/button'
import PublicNavBar from '@/components/shared/PublicNavBar.vue'
import PublicFooter from '@/components/shared/PublicFooter.vue'
</script>
<template>
  <div class="min-h-screen bg-white">
    <PublicNavBar />

    <div class="container mx-auto py-8 px-4 max-w-4xl">
      <h1 class="text-2xl font-bold mb-4">Terms and Conditions</h1>
      <p class="mb-6">
        Welcome to the Aurora University Magazine Contribution System website. By accessing or using
        our website, you agree to the following terms and conditions. Please read them carefully.
      </p>

      <div class="space-y-8">
        <section>
          <h2 class="text-xl font-semibold mb-2">1. Acceptance of Terms</h2>
          <p>
            By using this website, you agree to comply with these Terms and Conditions, as well as
            any applicable university policies. If you do not agree, please do not use this website.
          </p>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">2. Use of the Website</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>
              The purpose of this website is to make it easier for students to submit, review, and
              choose their work for the university magazine.
            </li>
            <li>
              Users are responsible for making sure that any material they submit conforms with
              copyright regulations and university policies.
            </li>
            <li>
              Unauthorized usage is strictly forbidden, including data scraping, hacking, and other
              misuses.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">3. User Accounts</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>Users are responsible for maintaining the security of their login credentials.</li>
            <li>
              The university reserves the right to suspend or terminate accounts found to be in
              violation of these Terms.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">4. Content Submission and Ownership</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>
              Users who contribute content give the institution permission to use, edit, and publish
              their work in the magazine.
            </li>
            <li>
              Although users accept that published content might not be withdrawn upon request, they
              still retain ownership of their original work.
            </li>
            <li>
              The university reserves the right to reject or edit any submission that does not meet
              publication standards.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">5. Privacy and Data Protection</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>
              Personal information collected through this website will be handled in accordance with
              university data protection policies.
            </li>
            <li>
              User data will not be shared with third parties without consent, except as required by
              law.
            </li>
            <li>
              Analytics tools and cookies can be used to enhance user experience and website
              operation.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">6. Website Availability and Changes</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>
              The university aims to keep the website accessible at all times but does not guarantee
              uninterrupted service.
            </li>
            <li>Scheduled maintenance and updates may result in temporary downtimes.</li>
            <li>
              The university reserves the right to modify, suspend, or discontinue any part of the
              website at any time.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">7. Limitation of Liability</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>
              The university is not responsible for any loss or damage resulting from the use of
              this website.
            </li>
            <li>
              Users access the website at their own risk and are responsible for their own content
              submissions.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">8. Updates to Terms and Conditions</h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>
              These Terms and Conditions may be updated periodically to reflect changes in policies
              or legal requirements.
            </li>
            <li>
              Continued use of the website after updates signifies acceptance of the revised terms.
            </li>
          </ul>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-2">9. Contact Us</h2>
          <p>
            For any inquiries or support regarding this website, please contact the university's IT
            support team or your designated Faculty Marketing Coordinator. By using this website,
            you acknowledge that you have read, understood, and agreed to these Terms and
            Conditions.
          </p>
        </section>
      </div>
    </div>

    <PublicFooter />
  </div>
</template>
