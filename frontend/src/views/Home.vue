<script setup lang="ts">
import { ChevronRight } from 'lucide-vue-next'

import HeroLeft from '@/assets/hero-left.png'
import HeroRight from '@/assets/hero-right.png'
import Math from '@/assets/math.png'
import Quote from '@/assets/quote.png'
import Science from '@/assets/science.png'
import PublicFooter from '@/components/shared/PublicFooter.vue'
import PublicNavBar from '@/components/shared/PublicNavBar.vue'
import { Button } from '@/components/ui/button'
</script>
<template>
  <div class="min-h-screen bg-white">
    <PublicNavBar />

    <!-- Hero Section -->
    <div class="2xl:container 2xl:px-4 mx-auto grid md:grid-cols-2 gap-0">
      <div class="relative">
        <img
          :src="HeroLeft"
          alt="Student reading"
          class="w-full h-[300px] lg:h-[500px] object-cover"
        />
        <div
          class="absolute inset-0 bg-black bg-opacity-30 flex flex-col justify-end items-center p-6 gap-4"
        >
          <div class="uppercase text-white text-xs tracking-wider mb-2">Featured</div>
          <h2 class="text-white text-2xl font-bold mb-2">Check out our Featured Articles</h2>
          <Button>Click Here</Button>
        </div>
      </div>

      <div class="relative">
        <img
          :src="HeroRight"
          alt="Campus buildings"
          class="w-full h-[300px] lg:h-[500px] object-cover"
        />
        <div
          class="absolute inset-0 bg-black bg-opacity-30 flex flex-col justify-end items-center p-6 gap-4"
        >
          <div class="text-white text-2xl font-bold mb-2">COMMUNITY OUTREACH</div>
          <Button>Read Articles</Button>
        </div>
      </div>
    </div>

    <!-- Magazine Section -->
    <div class="container mx-auto py-8 px-4">
      <div class="bg-gray-100 p-6 rounded-md">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <h2 class="text-4xl font-bold mb-4 md:mb-0">
            "Your Voice, Your Stories<br />
            Your Aurora Magazine"
          </h2>
          <div class="max-w-md">
            <p class="mb-2">Create and share your ideas by joining our faculty articles.</p>
            <p class="text-sm text-gray-600 italic">
              Do not miss the chance to get featured at Aurora Magazine.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured Articles -->
    <div class="container mx-auto px-4 py-6" id="articles">
      <h2 class="text-xl font-bold mb-4">Featured Articles</h2>
      <div class="border-b-4 border-primary w-[165px]"></div>

      <div class="space-y-8 mt-6">
        <!-- Article 1 -->
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <div class="md:w-1/3">
            <!-- <img src="/api/placeholder/320/200" alt="Basketball team" class="w-full h-40 object-cover rounded-md" /> -->
          </div>
          <div class="md:w-2/3">
            <h3 class="text-lg font-bold mb-2">
              Aurora University Basketball Team Reflects on Memorable Season
            </h3>
            <p class="text-gray-700 mb-2">
              The Aurora University men's basketball team concluded their latest season with a mix
              of pride and nostalgia.
            </p>
            <Button variant="link" class="flex items-center px-0">
              Read More <ChevronRight class="ml-1 h-4 w-4" />
            </Button>
          </div>
        </div>

        <!-- Article 2 -->
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <div class="md:w-1/3">
            <!-- <img src="/api/placeholder/320/200" alt="Basketball game" class="w-full h-40 object-cover rounded-md" /> -->
          </div>
          <div class="md:w-2/3">
            <h3 class="text-lg font-bold mb-2">
              Aurora University Basketball Team Reflects on Memorable Season
            </h3>
            <p class="text-gray-700 mb-2">
              The Aurora University men's basketball team concluded their latest season with a mix
              of pride and nostalgia.
            </p>
            <a href="#" class="text-primary hover:underline text-sm font-medium flex items-center">
              Read More
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </a>
          </div>
        </div>

        <!-- Article 3 -->
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <div class="md:w-1/3">
            <!-- <img src="/api/placeholder/320/200" alt="Basketball players" class="w-full h-40 object-cover rounded-md" /> -->
          </div>
          <div class="md:w-2/3">
            <h3 class="text-lg font-bold mb-2">
              Aurora University Basketball Team Reflects on Memorable Season
            </h3>
            <p class="text-gray-700 mb-2">
              The Aurora University men's basketball team concluded their latest season with a mix
              of pride and nostalgia.
            </p>
            <a href="#" class="text-primary hover:underline text-sm font-medium flex items-center">
              Read More
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="px-4 xl:container xl:px-0 flex flex-col gap-5 mt-6">
      <!-- Article 4 -->
      <div class="flex flex-col md:flex-row md:items-center gap-6 md:pr-6 pr-0">
        <div class="md:w-1/3">
          <img :src="Math" alt="Mathematics" class="w-full h-[350px] object-cover" />
        </div>
        <div class="md:w-2/3">
          <h3 class="text-2xl font-bold mb-6">Crunching Numbers, Shaping Futures</h3>
          <p class="text-gray-700 mb-2">
            The Mathematics Department at Aurora is making waves with its cutting-edge work and
            vibrant community. In 2024 alone, the department contributed to over 50 peer-reviewed
            publications, tackling everything from statistical modeling to pure number theory.
            Research funding reached an impressive $2.3 million last year, according to university
            records, fueling projects that apply math to climate science, digital security,
            cryptography, and beyond.
          </p>
        </div>
      </div>

      <!-- Article 5 -->
      <div class="flex flex-col md:flex-row-reverse md:items-center gap-6 md:pl-6 pl-0">
        <div class="md:w-1/3">
          <img :src="Science" alt="Science" class="w-full h-[350px] object-cover" />
        </div>
        <div class="md:w-2/3">
          <h3 class="text-2xl font-bold mb-6">Crunching Numbers, Shaping Futures</h3>
          <p class="text-gray-700 mb-2">
            The Mathematics Department at Aurora is making waves with its cutting-edge work and
            vibrant community. In 2024 alone, the department contributed to over 50 peer-reviewed
            publications, tackling everything from statistical modeling to pure number theory.
            Research funding reached an impressive $2.3 million last year, according to university
            records, fueling projects that apply math to climate science, digital security,
            cryptography, and beyond.
          </p>
          <p class="text-gray-700">
            The Mathematics Department at Aurora is making waves with its cutting-edge work and
            vibrant community.
          </p>
        </div>
      </div>
    </div>

    <!-- Quote Section -->

    <div class="flex flex-row gap-6 my-6 bg-primary text-white">
      <div class="hidden h-[300px] md:block">
        <img :src="Quote" alt="Quote" class="h-full w-full object-cover" />
      </div>
      <div class="flex flex-col gap-4 justify-center p-6">
        <blockquote class="text-2xl font-semibold">
          "Education is the most powerful weapon which you can use to change the world."
        </blockquote>
        <cite class="block mt-4 text-sm">- Nelson Mandela</cite>
      </div>
    </div>

    <!-- Events Section -->
    <div class="container mx-auto px-4 py-10" id="events">
      <h2 class="text-xl font-bold mb-4">Aurora University Upcoming Events</h2>
      <div class="border-b-4 border-primary w-[335px]"></div>

      <div class="grid md:grid-cols-2 gap-6 mt-6">
        <!-- Event 1 -->
        <div class="flex border rounded-md overflow-hidden">
          <div class="bg-gray-100 p-3 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-xs uppercase">APR</div>
            <div class="text-2xl font-bold">9</div>
          </div>
          <div class="p-4 flex-1">
            <h3 class="font-bold">Science Faculty Symposium</h3>
            <p class="text-sm text-gray-600 mb-2">Exploring the Frontiers of Quantum Biology</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Mon, Apr 7 at 2:00 pm
            </div>
          </div>
        </div>

        <!-- Event 2 -->
        <div class="flex border rounded-md overflow-hidden">
          <div class="bg-gray-100 p-3 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-xs uppercase">APR</div>
            <div class="text-2xl font-bold">10</div>
          </div>
          <div class="p-4 flex-1">
            <h3 class="font-bold">Mathematics Lecture Series</h3>
            <p class="text-sm text-gray-600 mb-2">
              The Beauty of Fractals in Nature and Technology
            </p>
            <div class="flex items-center text-xs text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Thur, Apr 10 at 10:00 am
            </div>
          </div>
        </div>

        <!-- Event 3 -->
        <div class="flex border rounded-md overflow-hidden">
          <div class="bg-gray-100 p-3 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-xs uppercase">APR</div>
            <div class="text-2xl font-bold">15</div>
          </div>
          <div class="p-4 flex-1">
            <h3 class="font-bold">IT Panel Discussion</h3>
            <p class="text-sm text-gray-600 mb-2">The Future of AI Ethics and Innovation</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Tue, Apr 15 at 3:00 pm
            </div>
          </div>
        </div>

        <!-- Event 4 -->
        <div class="flex border rounded-md overflow-hidden">
          <div class="bg-gray-100 p-3 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-xs uppercase">APR</div>
            <div class="text-2xl font-bold">18</div>
          </div>
          <div class="p-4 flex-1">
            <h3 class="font-bold">Engineering Workshop</h3>
            <p class="text-sm text-gray-600 mb-2">
              Sustainable Design: Building the Cities of Tomorrow
            </p>
            <div class="flex items-center text-xs text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Fri, Apr 18 at 2:00 pm
            </div>
          </div>
        </div>

        <!-- Event 5 -->
        <div class="flex border rounded-md overflow-hidden">
          <div class="bg-gray-100 p-3 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-xs uppercase">APR</div>
            <div class="text-2xl font-bold">21</div>
          </div>
          <div class="p-4 flex-1">
            <h3 class="font-bold">Art Exhibition Opening</h3>
            <p class="text-sm text-gray-600 mb-2">Technology Meets Creativity</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Mon, Apr 21 at 5:00 pm
            </div>
          </div>
        </div>

        <!-- Event 6 -->
        <div class="flex border rounded-md overflow-hidden">
          <div class="bg-gray-100 p-3 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-xs uppercase">APR</div>
            <div class="text-2xl font-bold">25</div>
          </div>
          <div class="p-4 flex-1">
            <h3 class="font-bold">Management Seminar</h3>
            <p class="text-sm text-gray-600 mb-2">Leadership in the Age of Disruption</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Fri, Apr 25 at 1:00 pm
            </div>
          </div>
        </div>
      </div>
    </div>


    <PublicFooter />
  </div>
</template>
