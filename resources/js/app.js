/**
 * BanglaPuzzle — Cart Store
 * ─────────────────────────
 * All cart data lives in localStorage under the key 'bp_cart'.
 * Every mutation fires a 'cart:updated' CustomEvent on window
 * so any page can react (badge, cart page, checkout page).
 *
 * Public API (global `Cart` object):
 *   Cart.add(item)          — item: { id, name, price, oldPrice, image, url, category, qty }
 *   Cart.remove(id)
 *   Cart.update(id, qty)    — qty <= 0 removes the item
 *   Cart.clear()
 *   Cart.getItems()         — returns array
 *   Cart.getCount()         — total item count (sum of qty)
 *   Cart.getTotal()         — total price
 */

(function () {
    const KEY = 'bp_cart';

    function load() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; }
        catch { return []; }
    }

    function save(items) {
        localStorage.setItem(KEY, JSON.stringify(items));
        window.dispatchEvent(new CustomEvent('cart:updated', { detail: { items } }));
    }

    window.Cart = {
        getItems() { return load(); },

        getCount() {
            return load().reduce((s, i) => s + (i.qty || 1), 0);
        },

        getTotal() {
            return load().reduce((s, i) => s + i.price * (i.qty || 1), 0);
        },

        add(item) {
            const items = load();
            const idx   = items.findIndex(i => i.id === item.id);
            if (idx > -1) {
                items[idx].qty = Math.min(99, (items[idx].qty || 1) + (item.qty || 1));
            } else {
                items.push({ ...item, qty: item.qty || 1 });
            }
            save(items);
        },

        remove(id) {
            save(load().filter(i => i.id !== id));
        },

        update(id, qty) {
            qty = parseInt(qty, 10);
            if (isNaN(qty) || qty <= 0) { this.remove(id); return; }
            qty = Math.min(99, Math.max(1, qty));
            const items = load();
            const idx   = items.findIndex(i => i.id === id);
            if (idx > -1) { items[idx].qty = qty; save(items); }
        },

        clear() {
            save([]);
        }
    };

    // ── Update cart badge everywhere ─────────────────────────────────────────
    function updateBadge() {
        const count   = Cart.getCount();
        const badges  = document.querySelectorAll('[data-cart-count]');
        badges.forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? '' : 'none';
        });
    }

    window.addEventListener('cart:updated', updateBadge);
    document.addEventListener('DOMContentLoaded', updateBadge);
})();
