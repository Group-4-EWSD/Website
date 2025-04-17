<script setup lang="ts">
import AboutUs from '@/assets/about-us.png'

import { Button } from '@/components/ui/button'
import PublicNavBar from '@/components/shared/PublicNavBar.vue'
import PublicFooter from '@/components/shared/PublicFooter.vue'
</script>
<template>
  <div class="min-h-screen bg-white">
    <PublicNavBar />

    <!-- Hero Section -->
    <div class="2xl:container 2xl:px-4 mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10">
      <img
        :src="AboutUs"
        alt="Student reading"
        class="w-full h-[300px] lg:h-screen object-cover"
      />

      <div class="flex flex-col justify-center mx-10 mb-10 lg:mb-0 lg:mx-0 lg:mr-10 gap-10">
        <img
          src="@/assets/logo.png"
          alt="University magazine logo"
          class="h-[200px] w-auto max-h-full object-contain"
        />
        <h3 class="text-3xl font-bold text-center">About Aurora University</h3>
        <div class="text-justify">
          <p>
            Aurora University (AU) is a private, nonprofit, accredited institution dedicated to
            providing an affordable, high-quality education that is student-centered and focused on
            teaching excellence. Founded in 1893 as Mendota Seminary in Mendota, Illinois, the
            institution relocated to Aurora in 1912 and adopted the name Aurora College, before
            becoming Aurora University in 1985.
          </p>
          <p>
            The university's mission is to empower students to achieve lasting personal and
            professional success through an inclusive community dedicated to the transformative
            power of learning. As a teaching-centered institution, AU encourages students to
            discover what it takes to build meaningful and purposeful lives. Aurora University is
            committed to celebrating diversity, advocating for equity, and practicing inclusivity,
            striving to create a welcoming environment for all learners.
          </p>
        </div>
      </div>
    </div>
    <PublicFooter />
  </div>
</template>
