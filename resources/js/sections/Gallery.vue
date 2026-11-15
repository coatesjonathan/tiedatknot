<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue'
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

const props = defineProps({ images: { type: Array, default: () => [] } })

const t = useCopy()

/** Ratio assumed for a photo until it loads and reports its real shape. */
const fallbackRatio = 1.5

/** Each photo's width ÷ height, keyed by index, read once it loads. */
const ratios = reactive({})

const grid = ref(null)
const gridWidth = ref(0)
let observer = null

onMounted(() => {
    observer = new ResizeObserver(([entry]) => {
        gridWidth.value = entry.contentRect.width
    })
    observer.observe(grid.value)
})

onBeforeUnmount(() => observer?.disconnect())

function recordRatio(index, event) {
    const { naturalWidth, naturalHeight } = event.target

    if (naturalWidth && naturalHeight) {
        ratios[index] = naturalWidth / naturalHeight
    }
}

/** How tall a row should roughly be at the current width. */
function targetRowHeight(width) {
    if (width >= 1024) {
        return 300
    }

    return width >= 768 ? 240 : 160
}

/**
 * Splits the photos into rows that each span the full width at one shared
 * height. The row count comes from the target height; the split is the one
 * whose rows are closest to equal, so no photo is left stranded on its own.
 */
const rows = computed(() => {
    const tiles = props.images.map((image, index) => ({
        image,
        index,
        ratio: ratios[index] ?? fallbackRatio,
    }))

    if (!tiles.length || !gridWidth.value) {
        return [tiles]
    }

    const totalRatio = tiles.reduce((sum, tile) => sum + tile.ratio, 0)
    const rowCount = Math.min(
        tiles.length,
        Math.max(1, Math.round((totalRatio * targetRowHeight(gridWidth.value)) / gridWidth.value)),
    )

    return partition(tiles, rowCount, totalRatio / rowCount)
})

/**
 * Contiguous split of `tiles` into `rowCount` rows minimising the squared
 * distance of each row's summed ratio from `ideal`.
 */
function partition(tiles, rowCount, ideal) {
    const count = tiles.length
    const prefix = [0]
    tiles.forEach((tile) => prefix.push(prefix.at(-1) + tile.ratio))

    const cost = Array.from({ length: rowCount + 1 }, () => Array(count + 1).fill(Infinity))
    const cut = Array.from({ length: rowCount + 1 }, () => Array(count + 1).fill(0))
    cost[0][0] = 0

    for (let row = 1; row <= rowCount; row++) {
        for (let end = row; end <= count; end++) {
            for (let start = row - 1; start < end; start++) {
                const candidate = cost[row - 1][start] + (prefix[end] - prefix[start] - ideal) ** 2

                if (candidate < cost[row][end]) {
                    cost[row][end] = candidate
                    cut[row][end] = start
                }
            }
        }
    }

    const result = []
    let end = count

    for (let row = rowCount; row > 0; row--) {
        const start = cut[row][end]
        result.unshift(tiles.slice(start, end))
        end = start
    }

    return result
}
</script>

<template>
    <RevealSection v-if="images.length" class="mx-auto max-w-[85rem] px-6 pt-22">
        <p class="mb-[1.625rem] text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.gallery') }}</p>

        <div ref="grid" class="flex flex-col gap-3.5">
            <div v-for="(row, rowIndex) in rows" :key="rowIndex" class="flex gap-3.5">
                <div
                    v-for="tile in row"
                    :key="tile.index"
                    class="min-w-0 basis-0 overflow-hidden bg-card"
                    :style="{ flexGrow: tile.ratio, aspectRatio: tile.ratio }"
                >
                    <img
                        :src="tile.image.image"
                        :alt="tile.image.caption ?? ''"
                        loading="lazy"
                        class="block size-full object-cover"
                        @load="recordRatio(tile.index, $event)"
                    />
                </div>
            </div>
        </div>
    </RevealSection>
</template>
