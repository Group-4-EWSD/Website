<script setup lang="ts">
import {
  ChevronRight,
  FacebookIcon,
  InstagramIcon,
  LinkedinIcon,
  TwitterIcon,
  YoutubeIcon,
} from 'lucide-vue-next'

import Logo from '@/assets/logo.png'
</script>
<template>
  <footer class="bg-gray-100 pt-10">
    <div class="container mx-auto px-4 py-8">
      <div class="grid md:grid-cols-3 gap-8">
        <!-- Logo and Magazine -->
        <div class="flex items-start justify-center md:justify-start">
          <img :src="Logo" alt="Aurora University Logo" class="h-32 w-32 mr-4" />
          <div class="mt-4 flex flex-col gap-2">
            <div class="font-semibold text-lg tracking-wider">AURORA</div>
            <div class="font-semibold text-lg tracking-wider">UNIVERSITY</div>
            <div class="font-semibold text-lg tracking-wider">MAGAZINE</div>
          </div>
        </div>

        <!-- Social Media -->
        <div class="text-center md:text-left">
          <h3 class="text-xl font-semibold mb-6 tracking-widest">Our Social Media</h3>
          <div class="flex flex-col justify-center items-center md:grid md:grid-cols-3 gap-6">
            <a
              href="https://www.facebook.com"
              target="_blank"
              class="flex items-center text-gray-600 hover:text-blue-700"
            >
              <FacebookIcon class="h-6 min-w-6 mr-4" />
              Facebook
            </a>
            <a
              href="https://www.x.com"
              target="_blank"
              class="flex items-center text-gray-600 hover:text-blue-500"
            >
              <TwitterIcon class="h-6 min-w-6 mr-4" />
              Twitter
            </a>
            <a
              href="https://www.instagram.com"
              target="_blank"
              class="flex items-center text-gray-600 hover:text-pink-500"
            >
              <InstagramIcon class="h-6 min-w-6 mr-4" />
              Instagram
            </a>
            <a
              href="https://www.youtube.com"
              target="_blank"
              class="flex items-center text-gray-600 hover:text-red-600"
            >
              <YoutubeIcon class="h-6 min-w-6 mr-4" />
              Youtube
            </a>
            <a
              href="https://www.linkedin.com"
              target="_blank"
              class="flex items-center text-gray-600 hover:text-blue-600"
            >
              <LinkedinIcon class="h-6 min-w-6 mr-4" />
              LinkedIn
            </a>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="md:ml-24 text-center md:text-left text-sm">
          <h3 class="text-xl font-semibold mb-4 tracking-widest">Aurora University</h3>
          <p class="text-gray-600 mb-2">123 Knowledge Avenue</p>
          <p class="text-gray-600 mb-2">Bright City, BC 56789</p>
          <p class="text-gray-600 mb-2">Countryland</p>
          <p class="text-gray-600 mb-2">Phone: +09993099909</p>
          <p class="text-gray-600">Email: contactus@aurora.edu</p>
        </div>
      </div>
    </div>

    <div class="py-4 text-center text-xs text-primary-foreground bg-primary">
      © 2025 Aurora University. All rights reserved.
    </div>
    <div
      class="py-4 text-center text-xs text-primary bg-accent flex items-center justify-center gap-4"
    >
      <RouterLink to="/home#articles" class="hover:underline">Articles</RouterLink>
      <RouterLink to="/home#events" class="hover:underline">Events</RouterLink>
      <RouterLink to="/about" class="hover:underline">About</RouterLink>
      <RouterLink to="/contact-us" class="hover:underline">Contact Us</RouterLink>
      <RouterLink to="/terms-and-conditions" class="hover:underline"
        >Terms and Conditions</RouterLink
      >
    </div>
  </footer>
</template>
