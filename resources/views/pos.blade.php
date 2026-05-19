@extends($layout ?? 'layouts.admin')
@section('content')

<div id="pos-section">
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    </link> 

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --bg:       #f0f2f5;
        --surface:  #ffffff;
        --border:   #e4e7ec;
        --text:     #1a1d23;
        --muted:    #6b7280;
        --accent:   #2563eb;
        --accent-lt:#eff6ff;
        --green:    #16a34a;
        --green-lt: #f0fdf4;
        --red:      #dc2626;
        --amber:    #d97706;
        --radius:   12px;
        --shadow:   0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
        --shadow-lg:0 4px 24px rgba(0,0,0,.12);
        --font-head:'Jost', sans-serif;
        --font-body:'Jost', sans-serif;

        --transition:.18s ease;
    }

    #pos-section { font-family: var(--font-body); color: var(--text); }

    .pos-wrap {
        background: var(--bg);
        min-height: 100vh;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* ── TOP BAR ── */
    .pos-topbar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        box-shadow: var(--shadow);
    }

    .pos-brand {
        font-family: var(--font-head);
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .pos-brand .brand-dot {
        width: 8px; height: 8px;
        background: var(--green);
        border-radius: 50%;
        animation: blink 2s infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .topbar-stats {
        display: flex;
        gap: 10px;
        margin-left: 8px;
    }
    .tstat {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--muted);
        transition: var(--transition);
    }
    .tstat span { font-weight: 700; color: var(--text); }
    .tstat i { color: var(--accent); font-size: 11px; }

    .topbar-actions {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .t-btn {
        padding: 7px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text);
        font-family: var(--font-body);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
        white-space: nowrap;
    }
    .t-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); transform: translateY(-1px); box-shadow: 0 3px 10px rgba(37,99,235,.25); }
    .t-btn.danger:hover { background: var(--red); border-color: var(--red); }
    .t-btn.success-btn:hover { background: var(--green); border-color: var(--green); }

    .customer-select {
        padding: 7px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 13px;
        background: var(--bg);
        color: var(--text);
        cursor: pointer;
        transition: var(--transition);
        min-width: 180px;
    }
    .customer-select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

    /* ── MAIN LAYOUT ── */
    .pos-body {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 14px;
        flex: 1;
        min-height: 0;
    }

    /* ── PRODUCT PANEL ── */
    .panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .panel-head {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: var(--bg);
    }

    .panel-title {
        font-family: var(--font-head);
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--text);
        white-space: nowrap;
    }
    .panel-title i { color: var(--accent); }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-box i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 13px;
        pointer-events: none;
    }
    .search-box input {
        width: 100%;
        padding: 8px 12px 8px 32px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 13px;
        background: var(--surface);
        color: var(--text);
        transition: var(--transition);
    }
    .search-box input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .search-box input::placeholder { color: var(--muted); }

    .sort-select {
        padding: 7px 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 13px;
        background: var(--surface);
        color: var(--text);
        cursor: pointer;
    }
    .sort-select:focus { outline: none; border-color: var(--accent); }

    /* Filters */
    .filter-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-bottom: 1px solid var(--border);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .filter-row::-webkit-scrollbar { display: none; }

    .filter-chip {
        padding: 5px 13px;
        border-radius: 20px;
        border: 1px solid var(--border);
        background: var(--surface);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: var(--transition);
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .filter-chip:hover { border-color: var(--accent); color: var(--accent); }
    .filter-chip.active { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* Product Grid */
    .product-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        align-content: start;
    }
    .product-scroll::-webkit-scrollbar { width: 5px; }
    .product-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    .prod-card {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 14px 12px 12px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .prod-card:hover {
        border-color: var(--accent);
        box-shadow: 0 4px 20px rgba(37,99,235,.12);
        transform: translateY(-2px);
    }
    .prod-card:active { transform: scale(.97); }

    .prod-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--red);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 6px;
    }

    .prod-img-wrap {
        width: 64px;
        height: 64px;
        border-radius: 10px;
        overflow: hidden;
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2px;
        flex-shrink: 0;
    }
    .prod-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .25s ease;
    }
    .prod-card:hover .prod-img-wrap img { transform: scale(1.08); }

    .prod-name {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.3;
        max-height: 2.6em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .prod-price {
        font-family: var(--font-head);
        font-size: 14px;
        font-weight: 700;
        color: var(--accent);
    }

    .prod-stock {
        font-size: 11px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .prod-stock.ok   { color: var(--green); }
    .prod-stock.low  { color: var(--amber); }
    .prod-stock.none { color: var(--red); }

    .prod-add-btn {
        margin-top: 4px;
        width: 100%;
        padding: 6px;
        border: 1.5px solid var(--accent);
        border-radius: 8px;
        background: var(--accent-lt);
        color: var(--accent);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .prod-add-btn:hover { background: var(--accent); color: #fff; }
    .prod-add-btn:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

    /* ── CART PANEL ── */
    .cart-panel {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 140px);
        position: sticky;
        top: 0;
    }

    .cart-head {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg);
    }
    .cart-head-title {
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cart-head-title i { color: var(--accent); }
    .cart-badge {
        background: var(--accent);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        min-width: 24px;
        text-align: center;
    }

    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }
    .cart-items::-webkit-scrollbar { width: 4px; }
    .cart-items::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

    .cart-empty {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: var(--muted);
        text-align: center;
        padding: 20px;
    }
    .cart-empty i { font-size: 3rem; opacity: .25; }
    .cart-empty p { font-size: 13px; font-weight: 500; }

    .cart-item {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: var(--transition);
        animation: slideIn .2s ease;
    }
    @keyframes slideIn { from{opacity:0;transform:translateX(16px)} to{opacity:1;transform:none} }
    .cart-item:hover { border-color: var(--accent); background: var(--accent-lt); }

    .ci-info { flex: 1; min-width: 0; }
    .ci-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ci-price { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .ci-total { font-size: 13px; font-weight: 700; color: var(--accent); text-align: right; white-space: nowrap; }

    .qty-ctrl {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 3px 6px;
    }
    .qty-btn {
        width: 22px; height: 22px;
        border: none;
        border-radius: 5px;
        background: transparent;
        color: var(--accent);
        font-size: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        line-height: 1;
    }
    .qty-btn:hover { background: var(--accent); color: #fff; }
    .qty-num { font-size: 13px; font-weight: 700; min-width: 20px; text-align: center; }

    .ci-remove {
        width: 26px; height: 26px;
        border: none;
        border-radius: 6px;
        background: #fee2e2;
        color: var(--red);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: var(--transition);
        flex-shrink: 0;
    }
    .ci-remove:hover { background: var(--red); color: #fff; }

    /* Cart Footer */
    .cart-footer {
        padding: 14px;
        border-top: 1px solid var(--border);
        background: var(--surface);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .summary-lines { display: flex; flex-direction: column; gap: 5px; }
    .summary-line {
        display: flex;
        justify-content: space-between;
        font-size: 12.5px;
        color: var(--muted);
    }
    .summary-line span:last-child { font-weight: 600; color: var(--text); }
    .summary-line.discount span:last-child { color: var(--red); }
    .summary-divider { height: 1px; background: var(--border); margin: 4px 0; }
    .summary-total {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-head);
        font-size: 17px;
        font-weight: 800;
        color: var(--text);
        padding-top: 4px;
    }
    .summary-total .total-val { color: var(--accent); }

    .payment-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .payment-row label {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .payment-select {
        flex: 1;
        padding: 7px 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 13px;
        background: var(--bg);
        color: var(--text);
    }
    .payment-select:focus { outline: none; border-color: var(--accent); }

    .checkout-btn {
        width: 100%;
        padding: 13px;
        background: var(--green);
        color: #fff;
        border: none;
        border-radius: var(--radius);
        font-family: var(--font-head);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 2px 12px rgba(22,163,74,.3);
        letter-spacing: .3px;
    }
    .checkout-btn:hover:not(:disabled) { background: #15803d; transform: translateY(-1px); box-shadow: 0 4px 20px rgba(22,163,74,.4); }
    .checkout-btn:disabled { background: var(--muted); cursor: not-allowed; box-shadow: none; transform: none; }

    /* ── NOTIFICATIONS ── */
    .notif-wrap {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-width: 320px;
        pointer-events: none;
    }

    .notif {
        background: var(--surface);
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: var(--shadow-lg);
        border-left: 4px solid;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        transform: translateX(340px);
        opacity: 0;
        transition: all .3s cubic-bezier(.34,1.56,.64,1);
        pointer-events: all;
        position: relative;
        overflow: hidden;
    }
    .notif.show { transform: none; opacity: 1; }
    .notif.success  { border-color: var(--green); }
    .notif.error    { border-color: var(--red); }
    .notif.warning  { border-color: var(--amber); }
    .notif.info     { border-color: var(--accent); }

    .notif-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
    .notif.success .notif-icon  { color: var(--green); }
    .notif.error .notif-icon    { color: var(--red); }
    .notif.warning .notif-icon  { color: var(--amber); }
    .notif.info .notif-icon     { color: var(--accent); }

    .notif-body { flex: 1; }
    .notif-title { font-weight: 700; font-size: 13px; color: var(--text); }
    .notif-msg   { font-size: 12px; color: var(--muted); margin-top: 1px; }

    .notif-close {
        background: none;
        border: none;
        color: var(--muted);
        cursor: pointer;
        font-size: 13px;
        padding: 0;
        line-height: 1;
        margin-top: 1px;
    }
    .notif-close:hover { color: var(--text); }

    .notif-bar {
        position: absolute;
        bottom: 0; left: 0;
        height: 3px;
        background: currentColor;
        opacity: .3;
        animation: notif-timer 4s linear forwards;
    }
    .notif.success .notif-bar { color: var(--green); }
    .notif.error   .notif-bar { color: var(--red); }
    .notif.warning .notif-bar { color: var(--amber); }
    .notif.info    .notif-bar { color: var(--accent); }
    @keyframes notif-timer { from{width:100%} to{width:0%} }

    /* ── RECEIPT MODAL ── */
    .modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.55);
        backdrop-filter: blur(4px);
        z-index: 8000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0; visibility: hidden;
        transition: var(--transition);
        padding: 20px;
    }
    .modal-backdrop.open { opacity: 1; visibility: visible; }

    .modal-box {
        background: var(--surface);
        border-radius: 16px;
        width: 100%;
        max-width: 420px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-lg);
        transform: scale(.92) translateY(20px);
        transition: transform .3s cubic-bezier(.34,1.56,.64,1);
        scrollbar-width: thin;
    }
    .modal-backdrop.open .modal-box { transform: scale(1) translateY(0); }

    .modal-head {
        padding: 24px 24px 20px;
        text-align: center;
        border-bottom: 1px solid var(--border);
    }
    .modal-head .check-icon {
        width: 56px; height: 56px;
        background: var(--green-lt);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .modal-head .check-icon i { font-size: 26px; color: var(--green); }
    .modal-head h3 { font-family: var(--font-head); font-size: 18px; font-weight: 800; color: var(--text); }
    .modal-head p { color: var(--muted); font-size: 13px; margin-top: 3px; }

    .receipt-body {
        padding: 20px 24px;
        font-size: 13px;
    }
    .r-store { text-align: center; margin-bottom: 16px; }
    .r-store h4 { font-family: var(--font-head); font-size: 16px; font-weight: 800; }
    .r-store p { color: var(--muted); font-size: 12px; }

    .r-meta {
        background: var(--bg);
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .r-meta-row { display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); }
    .r-meta-row span:last-child { font-weight: 600; color: var(--text); }

    .r-items { margin-bottom: 16px; }
    .r-item {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px dashed var(--border);
        font-size: 13px;
    }
    .r-item:last-child { border: none; }
    .r-item-left { color: var(--text); }
    .r-item-right { font-weight: 600; color: var(--accent); }

    .r-totals { border-top: 2px solid var(--border); padding-top: 12px; }
    .r-total-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--muted); margin-bottom: 6px; }
    .r-total-row span:last-child { font-weight: 600; color: var(--text); }
    .r-total-row.r-grand { font-family: var(--font-head); font-size: 16px; font-weight: 800; color: var(--text); margin-top: 8px; border-top: 1px solid var(--border); padding-top: 8px; }
    .r-total-row.r-grand span:last-child { color: var(--green); }

    .r-footer { text-align: center; margin-top: 16px; color: var(--muted); font-size: 12px; }

    .modal-actions {
        padding: 16px 24px;
        display: flex;
        gap: 10px;
        border-top: 1px solid var(--border);
    }
    .m-btn {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-family: var(--font-body);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: var(--transition);
    }
    .m-btn.print { background: var(--accent); color: #fff; }
    .m-btn.print:hover { background: #1d4ed8; }
    .m-btn.close-btn { background: var(--bg); color: var(--text); border: 1px solid var(--border); }
    .m-btn.close-btn:hover { background: var(--border); }

    /* ── DISCOUNT MODAL ── */
    .discount-modal {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 8001;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0; visibility: hidden;
        transition: var(--transition);
        padding: 20px;
    }
    .discount-modal.open { opacity: 1; visibility: visible; }
    .discount-box {
        background: var(--surface);
        border-radius: 14px;
        padding: 28px;
        width: 100%;
        max-width: 340px;
        box-shadow: var(--shadow-lg);
        transform: scale(.9);
        transition: transform .25s ease;
    }
    .discount-modal.open .discount-box { transform: scale(1); }
    .discount-box h4 { font-family: var(--font-head); font-size: 17px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .discount-box h4 i { color: var(--amber); }
    .discount-input-wrap { position: relative; margin-bottom: 16px; }
    .discount-input-wrap input {
        width: 100%;
        padding: 12px 40px 12px 14px;
        border: 2px solid var(--border);
        border-radius: 10px;
        font-family: var(--font-head);
        font-size: 22px;
        font-weight: 800;
        text-align: center;
        color: var(--text);
        transition: var(--transition);
    }
    .discount-input-wrap input:focus { outline: none; border-color: var(--amber); }
    .discount-input-wrap .pct { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--muted); font-weight: 700; }
    .discount-presets { display: flex; gap: 8px; margin-bottom: 16px; }
    .d-preset { flex: 1; padding: 8px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; transition: var(--transition); }
    .d-preset:hover { background: var(--amber); color: #fff; border-color: var(--amber); }
    .discount-actions { display: flex; gap: 8px; }
    .d-apply { flex: 1; padding: 10px; background: var(--amber); color: #fff; border: none; border-radius: 8px; font-family: var(--font-body); font-weight: 700; cursor: pointer; transition: var(--transition); }
    .d-apply:hover { background: #b45309; }
    .d-cancel { flex: 1; padding: 10px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; font-family: var(--font-body); font-weight: 600; cursor: pointer; transition: var(--transition); }
    .d-cancel:hover { background: var(--border); }

    /* ── KEYBOARD SHORTCUT BADGE ── */
    .kbd {
        font-size: 10px;
        padding: 1px 5px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 4px;
        color: var(--muted);
        font-family: monospace;
    }

    /* ── LOADING SPINNER ── */
    .spinner {
        width: 18px; height: 18px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }
    @keyframes spin { to{transform:rotate(360deg)} }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .pos-body { grid-template-columns: 1fr; }
        .cart-panel { height: auto; position: static; }
        .product-scroll { max-height: 50vh; }
    }
    @media (max-width: 600px) {
        .pos-wrap { padding: 8px; gap: 8px; }
        .pos-topbar { padding: 10px 12px; }
        .topbar-stats { display: none; }
        .product-scroll { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
        .notif-wrap { left: 10px; right: 10px; max-width: none; top: 10px; }
    }

    @media print {
        body * { visibility: hidden !important; }
        .modal-box, .modal-box * { visibility: visible !important; }
        .modal-box { position: fixed; top: 0; left: 0; width: 100%; box-shadow: none; border-radius: 0; }
        .modal-actions { display: none !important; }
    }
</style>

<div class="pos-wrap">

    <!-- TOP BAR -->
    <div class="pos-topbar">
        <div class="pos-brand">
            <i class="fas fa-store-alt"></i>
            Smart POS
            <div class="brand-dot"></div>
        </div>

        <div class="topbar-stats">
            <div class="tstat">
                <i class="fas fa-chart-bar"></i>
                Today: <span id="statSales">Ksh 0</span>
            </div>
            <div class="tstat">
                <i class="fas fa-receipt"></i>
                Txns: <span id="statTxns">0</span>
            </div>
            <div class="tstat">
                <i class="fas fa-arrow-trend-up"></i>
                Avg: <span id="statAvg">Ksh 0</span>
            </div>
        </div>

        <div class="topbar-actions">
            <select class="customer-select" id="customerType">
                <option value="walkin"><i class="fas fa-walking"></i> Walk-in Customer</option>
                @isset($customers)
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                @endisset
            </select>

            <button class="t-btn" onclick="openDiscount()" title="Apply Discount [F3]">
                <i class="fas fa-percent"></i> Discount
                <span class="kbd">F3</span>
            </button>
            <button class="t-btn danger" onclick="clearCart()" title="Clear Cart">
                <i class="fas fa-trash-alt"></i> Clear
            </button>
        </div>
    </div>

    <!-- BODY -->
    <div class="pos-body">

        <!-- PRODUCTS -->
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="fas fa-boxes"></i>
                    Products
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="productSearch" placeholder="Search or scan barcode... (F1)">
                </div>
                <select class="sort-select" id="sortSelect">
                    <option value=""><i class="fas fa-sort"></i> Sort</option>
                    <option value="name_asc">Name A→Z</option>
                    <option value="name_desc">Name Z→A</option>
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="stock_asc">Stock ↑</option>
                    <option value="stock_desc">Stock ↓</option>
                </select>
            </div>

            <div class="filter-row" id="filterTabs">
                <button class="filter-chip active" data-category="all">
                    <i class="fas fa-th-large"></i> All
                </button>
                @isset($categories)
                    @foreach($categories as $cat)
                        <button class="filter-chip" data-category="{{ $cat->id }}">
                            <i class="fas fa-tag"></i> {{ $cat->name }}
                        </button>
                    @endforeach
                @endisset
            </div>

            <div class="product-scroll" id="productGrid">
                @foreach($products as $product)
                    <div class="prod-card"
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->sale_price ?? $product->regular_price }}"
                        data-stock="{{ $product->stock_quantity ?? $product->quantity }}"
                        data-category="{{ $product->category_id ?? 'general' }}">

                        @if(isset($product->sale_price) && isset($product->regular_price) && $product->regular_price > $product->sale_price)
                            <span class="prod-badge">
                                {{ round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) }}% OFF
                            </span>
                        @endif

                        <div class="prod-img-wrap">
                            <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}"
                                 alt="{{ $product->name }}"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23f0f2f5%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2220%22 fill=%22%23d1d5db%22%3E&#xf466;%3C/text%3E%3C/svg%3E'">
                        </div>

                        <div class="prod-name">{{ $product->name }}</div>
                        <div class="prod-price">Ksh {{ number_format($product->sale_price ?? $product->regular_price) }}</div>

                        <div class="prod-stock {{ $product->stock_status === 'outofstock' ? 'none' : ($product->stock_status === 'lowstock' ? 'low' : 'ok') }}">
                            <i class="fas fa-{{ $product->stock_status !== 'outofstock' ? 'circle-check' : 'circle-xmark' }}"></i>
                            {{ $product->stock_status !== 'outofstock' ? 'Stock: '.$product->stock_quantity : 'Out of Stock' }}
                        </div>

                        <button class="prod-add-btn add-to-cart"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->sale_price ?? $product->regular_price }}"
                            data-stock="{{ $product->stock_quantity }}"
                            {{ $product->stock_status === 'outofstock' ? 'disabled' : '' }}>
                            <i class="fas fa-cart-plus"></i>
                            {{ $product->stock_status !== 'outofstock' ? 'Add' : 'Unavailable' }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CART -->
        <div class="panel cart-panel">
            <div class="cart-head">
                <div class="cart-head-title">
                    <i class="fas fa-shopping-cart"></i> Cart
                </div>
                <span class="cart-badge" id="cartCount">0</span>
            </div>

            <div class="cart-items" id="cartItems">
                <div class="cart-empty">
                    <i class="fas fa-cart-shopping"></i>
                    <p>Your cart is empty</p>
                    <small>Click a product to add it</small>
                </div>
            </div>

            <div class="cart-footer">
                <div class="summary-lines" id="summaryLines" style="display:none">
                    <div class="summary-line">
                        <span><i class="fas fa-list-ul"></i> Subtotal</span>
                        <span id="sumSubtotal">Ksh 0</span>
                    </div>
                    <div class="summary-line discount" id="sumDiscountRow" style="display:none">
                        <span><i class="fas fa-percent"></i> Discount</span>
                        <span id="sumDiscount">– Ksh 0</span>
                    </div>
                    <div class="summary-divider"></div>
                </div>

                <div class="summary-total">
                    <span><i class="fas fa-money-bill-wave" style="color:var(--accent);margin-right:6px"></i>Total</span>
                    <span class="total-val" id="cartTotal">Ksh 0</span>
                </div>

                <div class="payment-row">
                    <label><i class="fas fa-credit-card"></i> Pay</label>
                    <select class="payment-select" id="paymentMethod">
                        <option value="cash"><i class="fas fa-money-bill"></i> Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <button class="checkout-btn" id="checkoutBtn" disabled>
                    <i class="fas fa-check-circle"></i> Complete Sale
                    <span class="kbd" style="background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.3)">F4</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- NOTIFICATIONS -->
<div class="notif-wrap" id="notifWrap"></div>

<!-- RECEIPT MODAL -->
<div class="modal-backdrop" id="receiptModal">
    <div class="modal-box">
        <div class="modal-head">
            <div class="check-icon"><i class="fas fa-check"></i></div>
            <h3>Sale Complete!</h3>
            <p>Transaction processed successfully</p>
        </div>
        <div class="receipt-body" id="receiptBody"></div>
        <div class="modal-actions">
            <button class="m-btn print" onclick="printReceipt()">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <button class="m-btn close-btn" onclick="closeReceipt()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<!-- DISCOUNT MODAL -->
<div class="discount-modal" id="discountModal">
    <div class="discount-box">
        <h4><i class="fas fa-percent"></i> Apply Discount</h4>
        <div class="discount-input-wrap">
            <input type="number" id="discountInput" min="0" max="100" value="0" placeholder="0">
            <span class="pct">%</span>
        </div>
        <div class="discount-presets">
            <button class="d-preset" onclick="setDiscount(5)">5%</button>
            <button class="d-preset" onclick="setDiscount(10)">10%</button>
            <button class="d-preset" onclick="setDiscount(15)">15%</button>
            <button class="d-preset" onclick="setDiscount(20)">20%</button>
        </div>
        <div class="discount-actions">
            <button class="d-apply" onclick="applyDiscount()">
                <i class="fas fa-check"></i> Apply
            </button>
            <button class="d-cancel" onclick="closeDiscount()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </div>
</div>

<script>
    /* ═══════════════════════════════════════════
       STATE
    ═══════════════════════════════════════════ */
    let cart = [];
    let discountPct = 0;
    let receiptSeq = 1000;
    let todaySales = 0;
    let todayTxns = 0;

    /* ═══════════════════════════════════════════
       PERSISTENCE
    ═══════════════════════════════════════════ */
    function loadState() {
        try {
            const d = JSON.parse(localStorage.getItem('pos_v3') || '{}');
            todaySales = d.sales || 0;
            todayTxns  = d.txns  || 0;
            receiptSeq = d.seq   || 1000;
            refreshStats();
        } catch(e) {}
    }

    function saveState() {
        try {
            localStorage.setItem('pos_v3', JSON.stringify({
                sales: todaySales, txns: todayTxns, seq: receiptSeq
            }));
        } catch(e) {}
    }

    function refreshStats() {
        document.getElementById('statSales').textContent = `Ksh ${todaySales.toLocaleString()}`;
        document.getElementById('statTxns').textContent  = todayTxns;
        const avg = todayTxns > 0 ? Math.round(todaySales / todayTxns) : 0;
        document.getElementById('statAvg').textContent   = `Ksh ${avg.toLocaleString()}`;
    }

    /* ═══════════════════════════════════════════
       NOTIFICATIONS
    ═══════════════════════════════════════════ */
    const icons = { success:'fa-circle-check', error:'fa-circle-xmark', warning:'fa-triangle-exclamation', info:'fa-circle-info' };

    function notify(type, title, msg, ms = 4000) {
        const wrap = document.getElementById('notifWrap');
        const el = document.createElement('div');
        el.className = `notif ${type}`;
        el.innerHTML = `
            <i class="fas ${icons[type]} notif-icon"></i>
            <div class="notif-body">
                <div class="notif-title">${title}</div>
                ${msg ? `<div class="notif-msg">${msg}</div>` : ''}
            </div>
            <button class="notif-close" onclick="this.closest('.notif').remove()"><i class="fas fa-xmark"></i></button>
            <div class="notif-bar"></div>
        `;
        wrap.appendChild(el);
        requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('show')));
        setTimeout(() => {
            el.classList.remove('show');
            setTimeout(() => el.remove(), 350);
        }, ms);

        // chime
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const freqs = { success:[523,659], error:[220,180], warning:[440,370], info:[523,587] };
            (freqs[type] || [440]).forEach((f, i) => {
                setTimeout(() => {
                    const o = ctx.createOscillator(), g = ctx.createGain();
                    o.connect(g); g.connect(ctx.destination);
                    o.frequency.value = f;
                    o.type = type === 'success' ? 'sine' : 'triangle';
                    g.gain.setValueAtTime(.08, ctx.currentTime);
                    g.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .25);
                    o.start(); o.stop(ctx.currentTime + .25);
                }, i * 120);
            });
        } catch(e) {}
    }

    /* ═══════════════════════════════════════════
       CART LOGIC
    ═══════════════════════════════════════════ */
    function addToCart(id, name, price, stock) {
        // Prevent adding out-of-stock items
        if (stock <= 0) {
            notify('error', 'Out of Stock', `${name} is not available`); 
            return;
        }

        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.qty >= stock) {
                notify('warning', 'Stock Limit', `Only ${stock} available for ${name}`); return;
            }
            existing.qty++;
            existing.total = existing.qty * existing.price;
            notify('info', 'Qty Updated', `${name} → ${existing.qty}`);
        } else {
            cart.push({ id, name, price, qty: 1, total: price, stock });
            notify('success', 'Added', name);
        }

        if (stock <= 5 && stock > 0) {
            notify('warning', 'Low Stock', `${name} has only ${stock} left`);
        }
        renderCart();
    }

    function removeFromCart(id) {
        const item = cart.find(i => i.id === id);
        if (item) notify('info', 'Removed', item.name);
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function changeQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;
        const nq = item.qty + delta;
        if (nq <= 0) { removeFromCart(id); return; }
        if (nq > item.stock) { notify('warning', 'Stock Limit', `Only ${item.stock} available`); return; }
        item.qty = nq;
        item.total = nq * item.price;
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        if (!confirm('Clear all items from cart?')) return;
        cart = []; discountPct = 0;
        renderCart();
        notify('info', 'Cart Cleared', 'All items removed');
    }

    function calcTotals() {
        const subtotal       = cart.reduce((s, i) => s + i.total, 0);
        const discountAmt    = subtotal * (discountPct / 100);
        const afterDiscount  = subtotal - discountAmt;
        const grand = afterDiscount;
        return { subtotal, discountAmt, grand };
    }

    function renderCart() {
        const box    = document.getElementById('cartItems');
        const count  = document.getElementById('cartCount');
        const btnCO  = document.getElementById('checkoutBtn');
        const totEl  = document.getElementById('cartTotal');
        const sumSec = document.getElementById('summaryLines');

        count.textContent = cart.length;

        if (cart.length === 0) {
            box.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-cart-shopping"></i>
                    <p>Your cart is empty</p>
                    <small>Click a product to add it</small>
                </div>`;
            totEl.textContent = 'Ksh 0';
            btnCO.disabled = true;
            sumSec.style.display = 'none';
            return;
        }

        box.innerHTML = cart.map(item => `
            <div class="cart-item" id="ci-${item.id}">
                <div class="ci-info">
                    <div class="ci-name" title="${item.name}">${item.name}</div>
                    <div class="ci-price">Ksh ${item.price.toLocaleString()} × ${item.qty}</div>
                </div>
                <div class="qty-ctrl">
                    <button class="qty-btn" onclick="changeQty('${item.id}', -1)"><i class="fas fa-minus"></i></button>
                    <span class="qty-num">${item.qty}</span>
                    <button class="qty-btn" onclick="changeQty('${item.id}', 1)"><i class="fas fa-plus"></i></button>
                </div>
                <div class="ci-total">Ksh ${item.total.toLocaleString()}</div>
                <button class="ci-remove" onclick="removeFromCart('${item.id}')"><i class="fas fa-trash"></i></button>
            </div>
        `).join('');

        const { subtotal, discountAmt, grand } = calcTotals();

        sumSec.style.display = 'flex';
        document.getElementById('sumSubtotal').textContent = `Ksh ${subtotal.toLocaleString()}`;

        const discRow = document.getElementById('sumDiscountRow');
        if (discountPct > 0) {
            discRow.style.display = 'flex';
            document.getElementById('sumDiscount').textContent = `– Ksh ${Math.round(discountAmt).toLocaleString()}`;
        } else {
            discRow.style.display = 'none';
        }

        totEl.textContent = `Ksh ${Math.round(grand).toLocaleString()}`;
        btnCO.disabled = false;
    }

    /* ═══════════════════════════════════════════
       SEARCH, FILTER, SORT
    ═══════════════════════════════════════════ */
    let searchTimer;
    document.getElementById('productSearch').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.prod-card').forEach(c => {
                const match = c.dataset.name.toLowerCase().includes(q);
                c.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (q && visible === 0) notify('info', 'No Results', `Nothing matched "${q}"`);
        }, 250);
    });

    document.getElementById('productSearch').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const q = this.value.trim().toLowerCase();
        if (!q) return;
        let hit = null;
        document.querySelectorAll('.prod-card').forEach(c => {
            if (c.dataset.name.toLowerCase() === q || c.dataset.id === q) hit = c;
        });
        if (hit) {
            const btn = hit.querySelector('.add-to-cart');
            if (btn && !btn.disabled) { btn.click(); this.value = ''; }
        } else {
            notify('warning', 'Not Found', `No product: "${this.value}"`);
        }
    });

    document.getElementById('filterTabs').addEventListener('click', function(e) {
        const chip = e.target.closest('.filter-chip');
        if (!chip) return;
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        const cat = chip.dataset.category;
        document.querySelectorAll('.prod-card').forEach(c => {
            c.style.display = (cat === 'all' || c.dataset.category === cat) ? '' : 'none';
        });
    });

    document.getElementById('sortSelect').addEventListener('change', function() {
        const val = this.value;
        const grid = document.getElementById('productGrid');
        const cards = [...document.querySelectorAll('.prod-card')];
        cards.sort((a, b) => {
            if (val === 'name_asc')   return a.dataset.name.localeCompare(b.dataset.name);
            if (val === 'name_desc')  return b.dataset.name.localeCompare(a.dataset.name);
            if (val === 'price_asc')  return +a.dataset.price - +b.dataset.price;
            if (val === 'price_desc') return +b.dataset.price - +a.dataset.price;
            if (val === 'stock_asc')  return +a.dataset.stock - +b.dataset.stock;
            if (val === 'stock_desc') return +b.dataset.stock - +a.dataset.stock;
            return 0;
        });
        cards.forEach(c => grid.appendChild(c));
    });

    /* ADD TO CART via event delegation */
    document.getElementById('productGrid').addEventListener('click', function(e) {
        const btn = e.target.closest('.add-to-cart');
        if (!btn || btn.disabled) return;
        const { id, name, price, stock } = btn.dataset;
        addToCart(id, name, parseFloat(price), parseInt(stock));
        // quick pulse
        const card = btn.closest('.prod-card');
        card.style.transform = 'scale(.96)';
        setTimeout(() => card.style.transform = '', 150);
    });

    /* ═══════════════════════════════════════════
       DISCOUNT
    ═══════════════════════════════════════════ */
    function openDiscount() {
        document.getElementById('discountInput').value = discountPct;
        document.getElementById('discountModal').classList.add('open');
        setTimeout(() => document.getElementById('discountInput').focus(), 50);
    }
    function closeDiscount() {
        document.getElementById('discountModal').classList.remove('open');
    }
    function setDiscount(val) {
        document.getElementById('discountInput').value = val;
    }
    function applyDiscount() {
        const v = parseFloat(document.getElementById('discountInput').value);
        if (isNaN(v) || v < 0 || v > 100) {
            notify('error', 'Invalid', 'Enter a value between 0 and 100'); return;
        }
        discountPct = v;
        renderCart();
        closeDiscount();
        notify('success', 'Discount Applied', `${v}% off the subtotal`);
    }

    /* ═══════════════════════════════════════════
       RECEIPT
    ═══════════════════════════════════════════ */
    function buildReceipt() {
        const { subtotal, discountAmt, grand } = calcTotals();
        const now = new Date();
        const rn = `RCP-${receiptSeq++}`;
        const pm = { cash:'Cash', mpesa:'M-Pesa', card:'Card' };
        const pay = pm[document.getElementById('paymentMethod').value] || 'Cash';

        document.getElementById('receiptBody').innerHTML = `
            <div class="r-store">
                <h4><i class="fas fa-store-alt"></i> YOUR STORE NAME</h4>
                <p>123 Main Street, Nairobi · +254-XXX-XXXXX</p>
            </div>
            <div class="r-meta">
                <div class="r-meta-row"><span><i class="fas fa-hashtag"></i> Receipt</span><span>${rn}</span></div>
                <div class="r-meta-row"><span><i class="fas fa-calendar-day"></i> Date</span><span>${now.toLocaleDateString()}</span></div>
                <div class="r-meta-row"><span><i class="fas fa-clock"></i> Time</span><span>${now.toLocaleTimeString()}</span></div>
                <div class="r-meta-row"><span><i class="fas fa-credit-card"></i> Payment</span><span>${pay}</span></div>
            </div>
            <div class="r-items">
                ${cart.map(i => `
                    <div class="r-item">
                        <div class="r-item-left">${i.name}<br><small style="color:var(--muted)">${i.qty} × Ksh ${i.price.toLocaleString()}</small></div>
                        <div class="r-item-right">Ksh ${i.total.toLocaleString()}</div>
                    </div>`).join('')}
            </div>
            <div class="r-totals">
                <div class="r-total-row"><span>Subtotal</span><span>Ksh ${subtotal.toLocaleString()}</span></div>
                ${discountPct > 0 ? `<div class="r-total-row"><span>Discount (${discountPct}%)</span><span style="color:var(--red)">– Ksh ${Math.round(discountAmt).toLocaleString()}</span></div>` : ''}
                <div class="r-total-row r-grand"><span>TOTAL</span><span>Ksh ${Math.round(grand).toLocaleString()}</span></div>
            </div>
            <div class="r-footer">
                <p><i class="fas fa-heart" style="color:var(--red)"></i> Thank you for shopping with us!</p>
                <p style="margin-top:4px">Please retain this receipt</p>
            </div>
        `;
    }

    function showReceipt() { document.getElementById('receiptModal').classList.add('open'); }
    function closeReceipt() { document.getElementById('receiptModal').classList.remove('open'); }
    function printReceipt() {
        window.print();
        notify('info', 'Printing', 'Receipt sent to printer');
    }

    document.getElementById('receiptModal').addEventListener('click', function(e) {
        if (e.target === this) closeReceipt();
    });

    /* ═══════════════════════════════════════════
       CHECKOUT
    ═══════════════════════════════════════════ */
    document.getElementById('checkoutBtn').addEventListener('click', async function() {
        if (cart.length === 0) return;
        const { grand } = calcTotals();
        const orig = this.innerHTML;
        this.innerHTML = '<span class="spinner"></span> Processing…';
        this.disabled = true;

        try {
            // Replace with real API call to your backend
            await new Promise(r => setTimeout(r, 900));

            todaySales += grand;
            todayTxns++;
            saveState();
            refreshStats();

            buildReceipt();
            showReceipt();

            notify('success', 'Sale Complete!',
                `Ksh ${Math.round(grand).toLocaleString()} via ${document.getElementById('paymentMethod').value.toUpperCase()}`);

            cart = []; discountPct = 0;
            renderCart();

        } catch(err) {
            notify('error', 'Checkout Failed', err.message);
            this.innerHTML = orig;
            this.disabled = false;
        }
    });

    /* ═══════════════════════════════════════════
       KEYBOARD SHORTCUTS
    ═══════════════════════════════════════════ */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape')         { closeReceipt(); closeDiscount(); }
        if (e.key === 'F1')             { e.preventDefault(); document.getElementById('productSearch').focus(); }
        if (e.key === 'F3')             { e.preventDefault(); openDiscount(); }
        if (e.key === 'F4')             { e.preventDefault(); document.getElementById('checkoutBtn').click(); }
        if (e.ctrlKey && e.key === 'p') {
            if (document.getElementById('receiptModal').classList.contains('open')) {
                e.preventDefault(); printReceipt();
            }
        }
    });

    /* ═══════════════════════════════════════════
       INIT
    ═══════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function() {
        loadState();
        document.getElementById('productSearch').focus();
        notify('info', 'POS Ready', 'F1 Search · F3 Discount · F4 Checkout');
    });
</script>

</div>

@endsection