{{-- Shared product items section for create/edit/modal forms --}}
{{-- Expects Alpine.js context with: formItems, isTutup, availableItems, addItem(), removeItem(), onItemSelect(), getItemStok(), totalItemsSubtotal, formatRupiah() --}}
<div class="rounded-xl border-2 transition-all duration-200" :class="isTutup ? 'border-gray-200 bg-gray-50 opacity-40' : 'border-blue-200 bg-blue-50/50'">
    <div class="flex items-center justify-between px-4 py-3 border-b" :class="isTutup ? 'border-gray-200' : 'border-blue-200'">
        <p class="font-semibold text-sm" :class="isTutup ? 'text-gray-400' : 'text-blue-800'">📦 Produk Terjual</p>
        <button type="button" @click="addItem()" :disabled="isTutup"
            class="flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg font-semibold transition-all"
            :class="isTutup ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
            Tambah
        </button>
    </div>

    <div class="p-4 space-y-2">
        <template x-if="formItems.length === 0 && !isTutup">
            <div class="text-center py-6">
                <p class="text-gray-400 text-sm">Belum ada produk</p>
                <p class="text-gray-300 text-xs mt-1">Klik "Tambah" untuk menambah produk</p>
            </div>
        </template>

        <template x-for="(fi, idx) in formItems" :key="idx">
            <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" x-text="idx + 1"></div>
                    <div class="flex-1 min-w-0">
                        <select :name="'items['+idx+'][item_id]'" x-model="fi.item_id" @change="onItemSelect(idx)"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-blue-400 focus:ring-1 focus:ring-blue-400" :disabled="isTutup" required>
                            <option value="">Pilih produk...</option>
                            <template x-for="item in availableItems" :key="item.id">
                                <option :value="item.id" x-text="item.nama"></option>
                            </template>
                        </select>
                    </div>
                    <div class="w-20 flex-shrink-0">
                        <input type="number" :name="'items['+idx+'][qty]'" x-model.number="fi.qty" min="1" placeholder="Qty"
                            class="w-full text-sm text-center border border-gray-200 rounded-lg px-2 py-2 focus:border-blue-400 focus:ring-1 focus:ring-blue-400" :disabled="isTutup" required>
                    </div>
                    <div class="w-28 flex-shrink-0 text-right">
                        <p class="text-sm font-bold text-blue-700" x-text="formatRupiah(fi.qty * fi.harga)"></p>
                        <p class="text-[10px] text-gray-400" x-show="fi.harga > 0" x-text="'@' + formatRupiah(fi.harga) + '/' + fi.satuan"></p>
                    </div>
                    <button type="button" @click="removeItem(idx)" class="flex-shrink-0 w-7 h-7 rounded-full bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="fi.item_id && getItemStok(fi.item_id) !== null">
                    <p class="text-[11px] mt-1.5 ml-10" :class="fi.qty > getItemStok(fi.item_id) ? 'text-red-500 font-medium' : 'text-gray-400'">
                        Stok tersedia: <span x-text="getItemStok(fi.item_id)"></span> <span x-text="fi.satuan"></span>
                        <span x-show="fi.qty > getItemStok(fi.item_id)"> — ⚠️ Melebihi stok!</span>
                    </p>
                </template>
            </div>
        </template>
    </div>

    <template x-if="formItems.length > 0">
        <div class="px-4 py-3 border-t flex justify-between items-center" :class="isTutup ? 'border-gray-200' : 'border-blue-200 bg-blue-100/50'">
            <span class="text-sm font-semibold" :class="isTutup ? 'text-gray-400' : 'text-blue-800'">Total Penjualan:</span>
            <span class="text-lg font-bold" :class="isTutup ? 'text-gray-400' : 'text-blue-700'" x-text="formatRupiah(totalItemsSubtotal)"></span>
        </div>
    </template>
</div>
