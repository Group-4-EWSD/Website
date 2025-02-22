<script setup lang="ts">
import { PencilIcon } from 'lucide-vue-next'

import { Card } from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
</script>

<template>
  <Card class="p-4">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead> Tile </TableHead>
          <TableHead> Submission Date </TableHead>
          <TableHead> Status </TableHead>
          <TableHead> Category </TableHead>
          <TableHead> Comments </TableHead>
          <TableHead> Actions </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow>
          <TableCell> Article 3 </TableCell>
          <TableCell> 12/12/2021 </TableCell>
          <TableCell> Pending </TableCell>
          <TableCell> Science </TableCell>
          <TableCell> 0 </TableCell>
          <TableCell>
            <div class="flex gap-2">
              <PencilIcon class="h-4 w-4" />
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>
