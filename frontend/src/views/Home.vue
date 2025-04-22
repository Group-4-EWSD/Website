<script setup lang="ts">
import { ChevronRight } from 'lucide-vue-next'

import Logo from '@/assets/logo.png'

import HeroLeft from '@/assets/hero-left.png'
import HeroRight from '@/assets/hero-right.png'
import Math from '@/assets/math.png'
import Quote from '@/assets/quote.png'
import Science from '@/assets/science.png'
import PublicFooter from '@/components/shared/PublicFooter.vue'
import PublicNavBar from '@/components/shared/PublicNavBar.vue'
import { Button } from '@/components/ui/button'
import { onMounted, ref } from 'vue'
import { getFeaturesArticles } from '@/api/articles'

// Interface for article data
interface FeaturedArticle {
  article_description: string
  article_title: string
  article_id: string | number
}

const featuredArticles = ref<FeaturedArticle[]>([])
const expandedArticles = ref<Record<string | number, boolean>>({})

// Fetch featured articles from API
onMounted(async () => {
  try {
    featuredArticles.value = (await getFeaturesArticles()) as unknown as FeaturedArticle[]
    console.log('--featuredArticles', featuredArticles.value)

    // Initialize all articles as collapsed
    featuredArticles.value.forEach((article) => {
      if (article.article_id) {
        expandedArticles.value[article.article_id] = false
      }
    })
  } catch (error) {
    console.error('Failed to fetch featured articles:', error)
    // Fallback data in case API fails
    featuredArticles.value = [
      {
        article_id: 1,
        article_title: 'Aurora University Basketball Team Reflects on Memorable Season',
        article_description:
          "The Aurora University men's basketball team concluded their latest season with a mix of pride and nostalgia. The team showcased exceptional talent and teamwork throughout the season, overcoming numerous challenges to secure their place in the university's sporting history.",
      },
      {
        article_id: 2,
        article_title: 'Faculty Research Spotlight: Advances in Environmental Science',
        article_description:
          "Aurora University's Environmental Science department has made significant breakthroughs in sustainable ecosystem management. Their latest research focuses on innovative approaches to urban green spaces and their impact on community well-being.",
      },
      {
        article_id: 3,
        article_title: 'Student-Led Initiative Transforms Campus Sustainability',
        article_description:
          'A group of dedicated students has successfully implemented a comprehensive recycling program across campus, significantly reducing waste and promoting environmental consciousness among the university community.',
      },
    ]
  }
})

// Function to toggle read more/less state
const toggleReadMore = (articleId: string | number) => {
  console.log('---', articleId)
  if (articleId) {
    expandedArticles.value[articleId] = !expandedArticles.value[articleId]

    console.log('---expanded ---> ', expandedArticles.value)
  }
}

// Function to get shortened description
const getShortenedDescription = (description: string): string => {
  const maxLength = 100
  return description.length > maxLength ? `${description.substring(0, maxLength)}...` : description
}

// Function to determine if article should show "Read more" button
const shouldShowReadMore = (description: string): boolean => {
  return description.length > 100
}
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
          <RouterLink to="/home#articles">
            <Button>Click Here</Button>
          </RouterLink>
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
          <RouterLink to="/auth/login">
            <Button>Read Articles</Button>
          </RouterLink>
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

    <!-- Featured Articles from API -->
    <div class="container mx-auto px-4 py-6" id="articles">
      <h2 class="text-xl font-bold mb-4">Featured Articles</h2>
      <div class="border-b-4 border-primary w-[165px]"></div>

      <div v-if="featuredArticles && featuredArticles.length" class="space-y-8 mt-6">
        <!-- Dynamic Articles from API -->
        <div
          v-for="(article, index) in featuredArticles"
          :key="index"
          class="flex flex-col md:flex-row md:items-center gap-6"
        >
          <div class="md:w-1/3">
            <!-- Placeholder image - would be real image from API in production -->
            <div
              class="w-full h-44 bg-gray-200 rounded-md flex items-center justify-center text-gray-500"
            >
              <img :src="Logo" class="h-full w-full object-contain"/>
            </div>
          </div>
          <div class="md:w-2/3">
            <h3 class="text-lg font-bold mb-2">{{ article.article_title }}</h3>
            <p class="text-gray-700 mb-2">
              <!-- <template v-if="article.article_id && expandedArticles[article.article_id]"> -->
              {{ article.article_description }}
              <!-- </template>
              <template v-else>
                {{ getShortenedDescription(article.article_description) }}
              </template> -->
            </p>
            <RouterLink to="/auth/login">
              <Button variant="link" class="flex items-center px-0">
                Read More <ChevronRight class="ml-1 h-4 w-4" />
              </Button>
            </RouterLink>
            <!-- <Button 
              v-if="shouldShowReadMore(article.article_description)" 
              variant="link" 
              class="flex items-center px-0"
              @click="toggleReadMore(article.article_id || index)"
            >
              <template v-if="article.article_id && expandedArticles[article.article_id]">
                Read Less <ChevronDown class="ml-1 h-4 w-4" />
              </template>
              <template v-else>
                Read More <ChevronRight class="ml-1 h-4 w-4" />
              </template>
            </Button> -->
          </div>
        </div>
      </div>

      <!-- Loading state or no articles message -->
      <div v-else class="py-8 text-center text-gray-500">
        <p>Loading featured articles...</p>
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
