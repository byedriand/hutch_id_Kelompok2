@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .page-title-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            animation: pageHeaderFadeIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(45, 125, 210, 0.04) 100%);
            border: 1.5px solid rgba(45, 125, 210, 0.15);
            border-radius: 1.75rem;
            padding: 2rem 2.25rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 16px 40px rgba(0, 82, 163, 0.1), inset 0 1px 2px rgba(255, 255, 255, 0.5);
        }
        .page-title-bar .page-heading {
            flex: 1;
            animation: contentSlideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .page-title-bar .page-heading h1 {
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            font-weight: 900;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
        }
        .page-title-bar .page-heading h1::before {
            content: '';
            display: inline-flex;
            width: 5px;
            height: 28px;
            background: linear-gradient(135deg, #0066cc, #0052a3);
            border-radius: 3px;
            animation: slideBarGrow 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            flex-shrink: 0;
            box-shadow: 0 6px 16px rgba(0, 102, 204, 0.35);
        }
        .page-title-bar .page-heading p {
            color: #64748b;
            margin-bottom: 0;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            font-weight: 600;
        }
        @keyframes pageHeaderFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
                filter: blur(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }
        @keyframes contentSlideIn {
            from {
                opacity: 0;
                transform: translateX(-15px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideBarGrow {
            from {
                height: 0;
                opacity: 0;
            }
            to {
                height: 28px;
                opacity: 1;
            }
        }
        .page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: center;
            animation: actionsSlideIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .page-actions .btn {
            border-radius: 1.1rem;
            font-weight: 800;
            padding: 0.85rem 2rem;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
            backdrop-filter: blur(8px);
            border: none;
        }
        .page-actions .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .page-actions .btn:hover::before {
            opacity: 1;
        }
        .page-actions .btn-primary {
            background: linear-gradient(135deg, #0066cc, #0052a3, #003d7a);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: 0 12px 32px rgba(0, 102, 204, 0.35);
        }
        .page-actions .btn-primary:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 18px 48px rgba(0, 82, 163, 0.45);
            border-color: rgba(255, 255, 255, 0.5);
        }
        .page-actions .btn-primary:active {
            transform: translateY(-2px) scale(1.01);
        }
        .page-actions .btn-primary i {
            font-size: 1rem;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .page-actions .btn-primary:hover i {
            transform: scale(1.15) rotate(45deg);
        }
        .page-actions .btn-outline-secondary {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1.5px solid rgba(45, 125, 210, 0.25);
            color: #2d7dd2;
            box-shadow: 0 8px 20px rgba(100, 116, 139, 0.15);
        }
        .page-actions .btn-outline-secondary:hover {
            transform: translateY(-4px) scale(1.03);
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
            border-color: #0066cc;
            color: #0052a3;
            box-shadow: 0 12px 32px rgba(0, 102, 204, 0.25);
        }
        .page-actions .btn-outline-secondary:active {
            transform: translateY(-2px);
        }
        @keyframes actionsSlideIn {
            from {
                opacity: 0;
                transform: translateX(15px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .page-title-bar {
            animation: slideDown 0.6s ease-out;
        }
        .card {
            border: 1.5px solid rgba(45, 125, 210, 0.18);
            border-radius: 1.8rem;
            box-shadow: 0 20px 50px rgba(0, 82, 163, 0.1), inset 0 1px 2px rgba(255, 255, 255, 0.6);
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            animation: cardFadeInUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
        }
        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 50%, rgba(45, 125, 210, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }
        .card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.8) 100%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .card:hover {
            border-color: rgba(45, 125, 210, 0.35);
            box-shadow: 0 30px 70px rgba(0, 82, 163, 0.18), inset 0 1px 2px rgba(255, 255, 255, 0.8);
            transform: translateY(-8px);
        }
        .card:hover::after {
            opacity: 1;
        }
        @keyframes cardFadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
                filter: blur(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }
        .card-header {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(45, 125, 210, 0.05) 100%) !important;
            border-bottom: 2px solid rgba(45, 125, 210, 0.15) !important;
            padding: 1.5rem 1.75rem !important;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            animation: headerSlideDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .card-header h5 {
            margin-bottom: 0;
            font-size: 1.1rem;
            letter-spacing: -0.01em;
            font-weight: 800;
            color: #1e293b;
            -webkit-background-clip: unset;
            -webkit-text-fill-color: unset;
            background-clip: unset;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            position: relative;
        }
        .card-header h5::before {
            content: '';
            display: inline-flex;
            width: 4px;
            height: 22px;
            background: linear-gradient(135deg, #0066cc, #0052a3);
            border-radius: 2px;
            flex-shrink: 0;
            animation: headerBarGrow 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }
        @keyframes headerSlideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes headerBarGrow {
            from {
                height: 0;
                opacity: 0;
            }
            to {
                height: 22px;
                opacity: 1;
            }
        }
        .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .summary-card {
            animation-delay: 0.15s;
        }
        .item-table tbody tr {
            transition: transform 0.25s ease, opacity 0.25s ease, background 0.25s ease;
        }
        .item-table tbody tr.added {
            animation: slideInUp 0.35s ease both;
        }
        .product-preview {
            transition: transform 0.25s ease;
        }
        .product-preview:hover {
            transform: translateX(3px);
        }
        .card-header {
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.08) 0%, rgba(0, 82, 163, 0.04) 100%) !important;
            border-bottom: 1.5px solid rgba(45, 125, 210, 0.12) !important;
            padding: 1.25rem 1.5rem !important;
            position: relative;
            z-index: 1;
        }
        .card-header h5 {
            margin-bottom: 0;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-header h5::before {
            content: '';
            display: inline-flex;
            width: 3px;
            height: 14px;
            background: linear-gradient(135deg, #0066cc, #0052a3);
            border-radius: 1px;
            flex-shrink: 0;
        }
        .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }
        /* Badge Styling - Modern & Premium */
        .badge {
            font-weight: 700;
            padding: 0.4rem 0.85rem !important;
            border-radius: 999px !important;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            animation: badgeSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .badge::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .badge::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
            transform: scale(0);
            transition: transform 0.6s ease;
            pointer-events: none;
        }
        .badge:hover {
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.2);
        }
        .badge:hover::before {
            opacity: 1;
        }
        .badge:active::after {
            transform: scale(2);
        }
        .badge.bg-success {
            background: linear-gradient(135deg, #10b981, #059669, #047857) !important;
            color: white !important;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35) !important;
        }
        .badge.bg-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626, #b91c1c) !important;
            color: white !important;
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35) !important;
        }
        .badge.bg-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8, #1e40af) !important;
            color: white !important;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4) !important;
        }
        .badge.bg-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706, #b45309) !important;
            color: white !important;
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.35) !important;
        }
        .badge.bg-info {
            background: linear-gradient(135deg, #06b6d4, #0891b2, #0e7490) !important;
            color: white !important;
            box-shadow: 0 8px 24px rgba(6, 182, 212, 0.35) !important;
        }
        @keyframes badgeSlideIn {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(0.88);
                filter: blur(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }
        /* Section Headers */
        .stock-verification h6,
        .card-header h6 {
            color: #0052a3;
            font-weight: 800;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .stock-verification h6::before {
            content: '';
            display: inline-flex;
            width: 2px;
            height: 12px;
            background: linear-gradient(135deg, #0066cc, #0052a3);
            border-radius: 1px;
            flex-shrink: 0;
        }
        .form-label {
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.01em;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            animation: labelSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        .form-label::before {
            content: '';
            width: 2px;
            height: 16px;
            background: linear-gradient(180deg, #0066cc, #0052a3);
            border-radius: 1px;
            flex-shrink: 0;
        }
        @keyframes labelSlideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .form-control,
        .form-select {
            border-radius: 1.1rem;
            border: 1.5px solid rgba(45, 125, 210, 0.2);
            min-height: 48px;
            box-shadow: 0 4px 12px rgba(0, 82, 163, 0.06), inset 0 1px 2px rgba(255, 255, 255, 0.8);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-weight: 600;
            color: #1e293b;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            position: relative;
            z-index: 1;
        }
        .form-control:hover,
        .form-select:hover {
            border-color: rgba(45, 125, 210, 0.35);
            box-shadow: 0 6px 18px rgba(0, 82, 163, 0.1), inset 0 1px 2px rgba(255, 255, 255, 0.8);
        }
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 4px rgba(45, 125, 210, 0.15), 0 12px 32px rgba(0, 82, 163, 0.15), inset 0 1px 2px rgba(255, 255, 255, 0.8);
            border-color: #0066cc;
            transform: translateY(-2px);
            outline: none;
        }
        .form-control::placeholder {
            color: #cbd5e1;
            font-weight: 600;
        }
        .form-control[readonly] {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-color: rgba(45, 125, 210, 0.15);
            color: #1e293b;
            font-weight: 700;
        }
        .input-group-text {
            border: none;
            background: transparent;
            color: #1e293b;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }
        .input-group-text:hover {
            color: #1e293b;
            transform: scale(1.1);
        }
        .cust-autocomplete {
            border-radius: 0 0 0.9rem 0.9rem;
            box-shadow: 0 12px 32px rgba(0, 82, 163, 0.12);
            margin-top: 0.15rem;
            background: #ffffff;
            border: 1.5px solid rgba(0, 102, 204, 0.15);
            border-top: none;
            animation: dropdownSlideDown 0.3s ease-out;
        }
        @keyframes dropdownSlideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        #cust-dropdown .dropdown-item {
            border-bottom: 1px solid rgba(0, 102, 204, 0.1);
            transition: all 0.25s ease;
            background: #ffffff;
            color: #1e293b;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }
        #cust-dropdown .dropdown-item:last-child {
            border-bottom: none;
        }
        #cust-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            color: #0066cc;
            border-left: 3px solid #0066cc;
            padding-left: calc(1rem - 3px);
            transform: translateX(3px);
        }
        #cust-dropdown .customer-name {
            font-weight: 800;
            font-size: 0.97rem;
            color: #1e293b;
        }
        #cust-dropdown .customer-meta {
            font-size: 0.85rem;
            color: #64748b;
        }
        #cust-dropdown .no-results {
            color: #475569;
            padding: 1rem;
        }
        .table-wrap {
            overflow-x: auto;
        }
        .item-table {
            min-width: 100%;
            table-layout: auto;
            border-collapse: separate;
            border-spacing: 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 1.3rem;
            overflow: hidden;
            animation: tableSlideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 12px 32px rgba(0, 82, 163, 0.08);
        }
        .item-table thead {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(45, 125, 210, 0.08) 100%);
            backdrop-filter: blur(10px);
        }
        .item-table th,
        .item-table td {
            padding: 1.2rem 1.1rem;
            vertical-align: middle;
            transition: all 0.3s ease;
        }
        .item-table th {
            color: #1e293b;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.78rem;
            border-bottom: 2px solid rgba(45, 125, 210, 0.15);
            position: relative;
            z-index: 1;
        }
        .item-table td {
            background: #ffffff;
            color: #475569;
            border-bottom: 1px solid rgba(219, 229, 241, 0.6);
            font-weight: 600;
        }
        .item-table tbody tr {
            animation: rowSlideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            transition: all 0.3s ease;
        }
        .item-table tbody tr:nth-child(1) { animation-delay: 0.08s; }
        .item-table tbody tr:nth-child(2) { animation-delay: 0.12s; }
        .item-table tbody tr:nth-child(3) { animation-delay: 0.16s; }
        .item-table tbody tr:last-child td {
            border-bottom: none;
        }
        .item-table tbody tr:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            box-shadow: inset 0 0 0 rgba(45, 125, 210, 0.1);
            transform: translateX(3px);
        }
        .item-table tbody tr:hover td {
            color: #1e293b;
            font-weight: 700;
        }
        @keyframes tableSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
                filter: blur(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }
        @keyframes rowSlideUp {
            from {
                opacity: 0;
                transform: translateY(10px) translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0) translateX(0);
            }
        }
        .product-preview {
            display: flex;
            gap: 0.85rem;
            align-items: center;
            width: 100%;
            min-width: 0;
        }
        .product-thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 1.25rem;
            object-fit: cover;
            flex-shrink: 0;
            background: linear-gradient(135deg, #eff6ff, #ffffff);
            border: 1px solid #dbeafe;
        }
        .product-preview-info {
            width: 100%;
            flex: 1;
            min-width: 0;
        }
        .product-preview-info .form-select {
            width: 100%;
            min-width: 220px;
        }
        .product-preview-info .product-info {
            font-size: 0.90rem;
            color: #334155;
            margin-top: 0.45rem;
            font-weight: 600;
        }
        .product-preview-info .product-name {
            display: block;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.35rem;
            font-size: 1.05rem;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .summary-widget {
            border-radius: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 1.75rem 1.5rem;
            border: 1.5px solid rgba(45, 125, 210, 0.2);
            box-shadow: 0 16px 40px rgba(0, 82, 163, 0.12), inset 0 1px 2px rgba(255, 255, 255, 0.6);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: widgetFadeInScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
        }
        .summary-widget::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 60%);
            pointer-events: none;
        }
        .summary-widget::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.5) 100%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .summary-widget:hover {
            border-color: rgba(45, 125, 210, 0.4);
            box-shadow: 0 24px 56px rgba(0, 82, 163, 0.2), inset 0 1px 2px rgba(255, 255, 255, 0.8);
            transform: translateY(-6px);
        }
        .summary-widget:hover::after {
            opacity: 1;
        }
        .summary-widget h6 {
            margin-bottom: 0.9rem;
            font-size: 0.8rem;
            color: #64748b;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 800;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .summary-widget h6::before {
            content: '';
            width: 2px;
            height: 14px;
            background: linear-gradient(180deg, #0066cc, #0052a3);
            border-radius: 1px;
        }
        .summary-widget .value {
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #0066cc, #0052a3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            position: relative;
            z-index: 1;
            animation: valueSlideUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .summary-widget .caption {
            margin-top: 0.6rem;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
            letter-spacing: 0.3px;
        }
        @keyframes widgetFadeInScale {
            from {
                opacity: 0;
                transform: scale(0.92) translateY(15px);
                filter: blur(6px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
                filter: blur(0);
            }
        }
        @keyframes valueSlideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .summary-footer {
            border-radius: 1.5rem;
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
            border: 1.5px solid rgba(45, 125, 210, 0.2);
            padding: 1.75rem;
            box-shadow: 0 16px 40px rgba(0, 82, 163, 0.12), inset 0 1px 2px rgba(255, 255, 255, 0.6);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: footerSlideUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            backdrop-filter: blur(8px);
        }
        .summary-footer:hover {
            border-color: rgba(45, 125, 210, 0.4);
            box-shadow: 0 24px 56px rgba(0, 82, 163, 0.2);
            transform: translateY(-4px);
        }
        @keyframes footerSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .container-fluid {
            animation: containerFadeIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        .animated-block {
            animation: blockSlideDown 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        .animated-block.delay-2 {
            animation-delay: 0.15s;
        }
        @keyframes containerFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes blockSlideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.3) 0%, transparent 100%);
            transform: scale(0);
            transition: transform 0.6s ease;
            pointer-events: none;
        }
        .btn:active::after {
            transform: scale(2);
        }
        .btn:active {
            transform: translateY(-1px);
        }
        .card {
            animation-delay: calc(var(--index, 0) * 80ms);
        }
        .card:nth-of-type(1) {
            --index: 1;
        }
        .card:nth-of-type(2) {
            --index: 2;
        }
        .card:nth-of-type(3) {
            --index: 3;
        }
        .summary-card {
            position: sticky;
            top: 2rem;
            border-color: rgba(45, 125, 210, 0.2);
            background: linear-gradient(135deg, #f8fbff 0%, #f0f9ff 100%);
            animation-delay: 0.3s;
            box-shadow: 0 20px 50px rgba(0, 82, 163, 0.15);
            border-radius: 1.8rem;
            animation: stickyCardSlideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        @keyframes stickyCardSlideIn {
            from {
                opacity: 0;
                transform: translateX(30px) scale(0.95);
                filter: blur(8px);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
                filter: blur(0);
            }
        }
        .table-responsive {
            animation: tableResponsiveFadeIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            animation-delay: 0.15s;
        }
        .table-responsive .item-table tbody tr {
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes tableResponsiveFadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .table-responsive .item-table tbody tr:hover {
            transform: translateX(2px);
            background: #f0f7ff !important;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideIn {
            from {
                width: 0;
                opacity: 0;
            }
            to {
                width: 4px;
                opacity: 1;
            }
        }
        @media (max-width: 1199px) {
            .page-title-bar .page-heading h1 {
                font-size: 1.3rem;
            }

            .page-title-bar .page-heading h1::before {
                height: 18px;
            }

            .summary-card {
                position: static;
            }

            .page-actions .btn {
                padding: 0.7rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 991px) {
            .page-title-bar {
                flex-direction: column;
                align-items: stretch;
                margin-bottom: 1.5rem;
            }

            .page-title-bar .page-heading h1 {
                font-size: 1.2rem;
            }

            .page-title-bar .page-heading h1::before {
                height: 16px;
            }

            .page-title-bar .page-heading p {
                font-size: 0.85rem;
            }

            .page-actions {
                justify-content: flex-start;
                width: 100%;
            }

            .page-actions .btn {
                flex: 1;
                font-size: 0.85rem;
                padding: 0.65rem 1.2rem;
                justify-content: center;
            }

            .summary-summary {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.85rem;
            }

            .summary-card {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .page-title-bar {
                gap: 0.75rem;
                margin-bottom: 1.3rem;
            }

            .page-title-bar .page-heading h1 {
                font-size: 1.1rem;
            }

            .page-title-bar .page-heading h1::before {
                height: 14px;
            }

            .page-title-bar .page-heading p {
                font-size: 0.8rem;
            }

            .page-actions {
                width: 100%;
                gap: 0.5rem;
            }

            .page-actions .btn {
                font-size: 0.8rem;
                padding: 0.6rem 1rem;
            }

            .summary-summary {
                grid-template-columns: 1fr;
            }

            .product-preview {
                flex-direction: column;
                align-items: stretch;
            }

            .product-thumbnail {
                width: 48px;
                height: 48px;
            }

            .product-preview-info {
                width: 100%;
            }

            .item-table thead th,
            .item-table td {
                padding: 0.8rem 0.7rem;
            }

            .item-table th:nth-child(2),
            .item-table th:nth-child(3),
            .item-table td:nth-child(2),
            .item-table td:nth-child(3) {
                min-width: 130px;
            }

            .item-table td {
                white-space: normal;
            }

            .stock-verification .table-wrap {
                overflow-x: auto;
            }
        }

        @media (max-width: 480px) {
            .page-title-bar .page-heading h1 {
                font-size: 1rem;
            }

            .page-title-bar .page-heading h1::before {
                width: 3px;
                height: 12px;
            }

            .page-title-bar .page-heading p {
                font-size: 0.75rem;
            }

            .page-actions {
                width: 100%;
                flex-direction: column;
            }

            .page-actions .btn {
                width: 100%;
                font-size: 0.75rem;
                padding: 0.55rem 0.9rem;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .card-header h5 {
                font-size: 0.95rem;
            }

            .card-body {
                padding: 1rem;
            }
        }
        .summary-label {
            font-size: 0.85rem;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .summary-label::before {
            content: '';
            width: 2px;
            height: 14px;
            background: linear-gradient(180deg, #0066cc, #0052a3);
            border-radius: 1px;
        }
        .summary-value {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            animation: valueCountUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes valueCountUp {
            from {
                opacity: 0;
                transform: translateY(15px);
                filter: blur(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Total PO Section - Premium Styling */
        .text-end.mt-3.rounded-4.bg-light.p-3 {
            border-radius: 1.5rem !important;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
            border: 1.5px solid rgba(45, 125, 210, 0.25) !important;
            padding: 1.75rem !important;
            box-shadow: 0 16px 40px rgba(0, 82, 163, 0.12), inset 0 1px 2px rgba(255, 255, 255, 0.6);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: totalPOSlideUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(8px);
        }
        .text-end.mt-3.rounded-4.bg-light.p-3:hover {
            border-color: rgba(45, 125, 210, 0.4);
            box-shadow: 0 24px 56px rgba(0, 82, 163, 0.2), inset 0 1px 2px rgba(255, 255, 255, 0.8);
            transform: translateY(-4px);
        }
        .text-end.mt-3.rounded-4.bg-light.p-3 .fw-semibold {
            color: #0052a3;
            font-size: 0.95rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-weight: 800;
        }
        .text-end.mt-3.rounded-4.bg-light.p-3 .text-primary {
            background: linear-gradient(135deg, #0066cc, #0052a3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            letter-spacing: -0.02em;
            font-size: 1.8rem;
            animation: totalValuePulse 1s ease-in-out infinite;
        }
        @keyframes totalPOSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
                filter: blur(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }
        @keyframes totalValuePulse {
            0%, 100% { filter: drop-shadow(0 0 0 rgba(0, 102, 204, 0)); }
            50% { filter: drop-shadow(0 0 8px rgba(0, 102, 204, 0.4)); }
        }
        /* Stock Verification - Enhanced Styling */
        .stock-verification {
            animation: stockVerificationFadeIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .stock-verification .alert {
            border-radius: 1.25rem;
            border: 1.5px solid rgba(239, 68, 68, 0.3);
            background: linear-gradient(135deg, rgba(254, 242, 242, 0.9) 0%, rgba(255, 248, 248, 0.9) 100%);
            color: #7f1d1d;
            animation: alertSlideDown 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 12px 32px rgba(239, 68, 68, 0.15);
            backdrop-filter: blur(8px);
            font-weight: 600;
        }
        .stock-verification .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706, #b45309) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 800;
            border-radius: 1.1rem;
            padding: 0.85rem 1.75rem;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 12px 32px rgba(245, 158, 11, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-size: 0.85rem;
        }
        .stock-verification .btn-warning:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow: 0 18px 48px rgba(245, 158, 11, 0.45);
            border-color: rgba(255, 255, 255, 0.5);
        }
        .stock-verification .btn-warning:active {
            transform: translateY(-2px) scale(1.02);
        }
        @keyframes stockVerificationFadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes alertSlideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
                filter: blur(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }
        /* Reduce notification badge size on Create PO page */
        #sidebar .badge {
            font-size: 0.45rem !important;
            padding: 0.1rem 0.3rem !important;
        }

        /* Premium Animated Popup Alert */
        .popup-alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            animation: overlayFadeIn 0.3s ease-out forwards;
        }

        @keyframes overlayFadeIn {
            from {
                opacity: 0;
                backdrop-filter: blur(0);
            }
            to {
                opacity: 1;
                backdrop-filter: blur(4px);
            }
        }

        .popup-alert {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 2rem;
            padding: 2.5rem 2rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25),
                        0 0 40px rgba(59, 130, 246, 0.15);
            animation: popupSlideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1.5px solid rgba(59, 130, 246, 0.15);
            position: relative;
            overflow: hidden;
        }

        .popup-alert::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: bubbleFloat 3s ease-in-out infinite;
        }

        @keyframes popupSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
                filter: blur(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        @keyframes bubbleFloat {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-20px) translateX(10px);
            }
        }

        .popup-alert.success {
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
            border-color: rgba(59, 130, 246, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15),
                        0 0 40px rgba(59, 130, 246, 0.2);
        }

        .popup-alert.error {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: rgba(37, 99, 235, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15),
                        0 0 40px rgba(37, 99, 235, 0.2);
        }

        .popup-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 2rem;
            animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(45, 125, 210, 0.1));
            border: 2px solid rgba(59, 130, 246, 0.3);
        }

        .popup-alert.success .popup-icon {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.1));
            border-color: rgba(59, 130, 246, 0.3);
            color: #3b82f6;
        }

        .popup-alert.error .popup-icon {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(29, 78, 216, 0.1));
            border-color: rgba(37, 99, 235, 0.3);
            color: #2563eb;
        }

        @keyframes iconBounce {
            0% {
                transform: scale(0) rotateZ(-180deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1) rotateZ(0deg);
                opacity: 1;
            }
        }

        .popup-title {
            font-size: 1.3rem;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 0.75rem;
            text-align: center;
            letter-spacing: -0.01em;
        }

        .popup-message {
            font-size: 0.95rem;
            color: #475569;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .popup-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            animation: buttonsSlideUp 0.6s ease-out 0.2s both;
        }

        @keyframes buttonsSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .popup-btn {
            padding: 0.85rem 2rem;
            border-radius: 1rem;
            border: none;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
        }

        .popup-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.3) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .popup-btn:hover::before {
            opacity: 1;
        }

        .popup-btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .popup-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(59, 130, 246, 0.4);
        }

        .popup-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .popup-btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.4);
        }

        .popup-btn-secondary {
            background: #f1f5f9;
            color: #2d7dd2;
            border: 1.5px solid rgba(45, 125, 210, 0.2);
        }

        .popup-btn-secondary:hover {
            background: #e2e8f0;
            border-color: rgba(45, 125, 210, 0.4);
            transform: translateY(-2px);
        }

        /* Close popup animation */
        .popup-alert.closing {
            animation: popupSlideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .popup-alert-overlay.closing {
            animation: overlayFadeOut 0.4s ease-out forwards;
        }

        @keyframes popupSlideDown {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
                filter: blur(8px);
            }
        }

        @keyframes overlayFadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    </style>

    <div class="page-title-bar animated-block">
        <div class="page-heading">
            <h1>Buat Pesanan Baru</h1>
            <p>Isi data pelanggan, pilih produk, dan kelola pesanan dengan cepat dalam satu laman.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" form="form-po" class="btn btn-primary" id="btn-simpan">
                <i class="fas fa-save me-2"></i>Simpan Pesanan
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="form-po" method="POST" action="{{ route('pesanan.store') }}">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-8">
                <!-- Card Informasi PO -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi PO</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_po" class="form-label">Nomor PO</label>
                                <input type="text" id="nomor_po" class="form-control mono" value="{{ $nomorPo }}" readonly>
                                <input type="hidden" name="nomor_po" value="{{ $nomorPo }}">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_pesanan" class="form-label">Tanggal Pesanan</label>
                                <input type="date" id="tanggal_pesanan" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                                <input type="hidden" name="tanggal_pesanan" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cust-input" class="form-label">Pelanggan *</label>
                            <div class="input-group position-relative">
                                <span class="input-group-text bg-white border-0 text-primary"><i class="fas fa-search"></i></span>
                                <input type="text" id="cust-input" class="form-control border-start-0" placeholder="Ketik nama pelanggan..." autocomplete="off">
                                <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                                <div id="cust-dropdown" class="cust-autocomplete position-absolute w-100" style="top: 100%; left: 0; display: none; max-height: 220px; overflow-y: auto; z-index: 1000;"></div>
                            </div>
                            <div class="mt-2 small text-muted">Pilih pelanggan yang sudah ada atau tambahkan dari menu Pelanggan.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cust-alamat" class="form-label">Alamat</label>
                                <textarea id="cust-alamat" class="form-control" readonly rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="cust-telepon" class="form-label">Telepon</label>
                                <input type="text" id="cust-telepon" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="cust-email" class="form-label">Email</label>
                                <input type="text" id="cust-email" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pengiriman" class="form-label">Tanggal Pengiriman *</label>
                            <input type="date" id="tanggal_pengiriman" name="tanggal_pengiriman" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('tanggal_pengiriman') }}" required>
                        </div>
                    </div>
                </div>

            <!-- Card Item Pesanan -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">Item Pesanan</h5>
                            <small class="text-muted">Tambah produk dan atur jumlah pesanan dengan mudah.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="tambahItem()">
                            <i class="fas fa-plus me-1"></i>Tambah Item
                        </button>
                    </div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table class="table table-sm item-table">
                            <thead>
                                <tr>
                                    <th style="width: 4%;">#</th>
                                    <th style="width: 30%;">Nama Produk</th>
                                    <th style="width: 9%;">Qty</th>
                                    <th style="width: 16%;">Harga Satuan</th>
                                    <th style="width: 26%;">Subtotal</th>
                                    <th style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                <tr id="item-1">
                                    <td>1</td>
                                    <td>
                                        <div class="product-preview">
                                            <img id="preview-1" src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='96' height='96'%3E%3Crect width='96' height='96' fill='%23eef2ff'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2364748b' font-size='12' font-family='Arial, sans-serif'%3ENo Image%3C/text%3E%3C/svg%3E" alt="Preview Produk" class="product-thumbnail">
                                            <div class="product-preview-info">
                                                <select name="items[1][produk_id]" class="form-select form-select-sm" onchange="updateHarga(this, 1)" required>
                                                    <option value="">-- Pilih Produk --</option>
                                                    @forelse($produk as $p)
                                                        <option value="{{ $p->id }}" data-harga="{{ $p->harga_jual }}" data-stok="{{ $p->stok }}">{{ $p->nama }}</option>
                                                    @empty
                                                        <option value="" disabled>Tidak ada produk tersedia. Tambahkan produk di menu Produk.</option>
                                                    @endforelse
                                                </select>
                                                <span id="preview-name-1" class="product-name d-none"></span>
                                                <div id="preview-info-1" class="product-info d-none">Pilih produk untuk melihat detail dan gambar.</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="items[1][jumlah]" class="form-control form-control-sm" min="1" value="1" onchange="hitungBaris(1)" required>
                                    </td>
                                    <td>
                                        <input type="text" id="harga-1" class="form-control form-control-sm mono" readonly>
                                        <input type="hidden" name="items[1][harga_satuan]" id="harga-hidden-1" value="0">
                                    </td>
                                    <td>
                                        <input type="text" id="sub-1" class="form-control form-control-sm mono" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="stock-verification mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0">Verifikasi Stok</h6>
                            <span id="stock-status-badge" class="badge rounded-pill bg-success text-white px-3">Semua stok cukup</span>
                        </div>
                        <div class="table-wrap">
                            <table class="table table-sm item-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Stok Tersedia</th>
                                        <th>Kebutuhan</th>
                                        <th>Selisih</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="stock-verification-body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Pilih produk untuk melihat verifikasi stok.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="stok-warning" class="alert alert-danger mt-3 d-none" role="alert"></div>
                        <div class="mt-3">
                            <button type="button" id="btn-notify-stok-kurang" class="btn btn-warning d-none" onclick="notifyStockShortage()">
                                <i class="fas fa-bell me-1"></i>Kirim Notifikasi Stok Kurang ke Operator Gudang
                            </button>
                        </div>
                    </div>
                    <div class="text-end mt-3 rounded-4 bg-light p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <span class="fw-semibold">Total PO</span>
                            <span id="total-po" class="mono text-primary fs-5">Rp 0</span>
                        </div>
                        <input type="hidden" name="total_nilai" id="total-nilai" value="0">
                        <input type="hidden" name="send_shortage_notification" id="send-shortage-notification" value="0">
                    </div>
                </div>
            </div>

            </div>
            <div class="col-12 col-xl-4">
                <div class="card summary-card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <span class="summary-label">Ringkasan Pesanan</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">Draft</span>
                            </div>
                            <p class="mb-3 text-muted">Lihat total item dan nilai pesanan sebelum menyimpan.</p>
                        </div>
                        <div class="row gx-3 gy-3 align-items-center mb-4 summary-summary">
                            <div class="col-sm-6">
                                <div>
                                    <div class="text-muted small">Total Item</div>
                                    <div class="summary-value" id="total-item">1</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div>
                                    <div class="text-muted small">Total Nilai</div>
                                    <div class="summary-value" id="summary-total">Rp 0</div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" form="form-po" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-2"></i>Simpan PO Sekarang
                        </button>
                        <div class="mt-3 small text-muted">
                            Pastikan semua item telah terisi dengan benar sebelum menyimpan pesanan.
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Catatan Khusus (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan tambahan untuk pesanan ini..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

@push('scripts')
@php
    $dataProduk = $produk->map(function ($p) {
        $gambar = $p->foto_url;
        
        // Fallback: generate placeholder berdasarkan nama produk
        if (!$gambar) {
            $namaEncoded = urlencode($p->nama);
            $gambar = "https://via.placeholder.com/200?text=" . $namaEncoded;
        }

        return [
            'id' => $p->id,
            'nama' => $p->nama,
            'harga' => $p->harga_jual,
            'stok' => $p->stok,
            'gambar' => $gambar,
        ];
    });
@endphp
<script>
const dataProduk = @json($dataProduk);
const placeholderProductImage = 'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'%3E%3Crect width=\'96\' height=\'96\' fill=\'%23eef2ff\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%2364748b\' font-size=\'12\' font-family=\'Arial, sans-serif\'%3ENo Image%3C/text%3E%3C/svg%3E';
let itemCount = 1;

const custDropdown = document.getElementById('cust-dropdown');
let autocompleteTimeout;

document.getElementById('cust-input').addEventListener('input', function() {
    clearTimeout(autocompleteTimeout);
    const q = this.value.trim();

    if (q.length < 2) {
        custDropdown.style.display = 'none';
        return;
    }

    autocompleteTimeout = setTimeout(() => {
        fetch(`/api/pelanggan/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                custDropdown.innerHTML = '';
                custDropdown.style.maxHeight = '220px';
                custDropdown.style.overflowY = 'auto';

                if (data.length === 0) {
                    const div = document.createElement('div');
                    div.className = 'text-muted p-3';
                    div.textContent = 'Tidak ada pelanggan ditemukan';
                    custDropdown.appendChild(div);
                    custDropdown.style.display = 'block';
                    return;
                }

                data.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'dropdown-item';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `
                        <span class="customer-name">${p.nama}</span>
                        <span class="customer-meta">${p.telepon || '-'} · ${p.email || 'Email tidak tersedia'}</span>
                    `;
                    div.onclick = () => pilihPelanggan(p);
                    custDropdown.appendChild(div);
                });

                custDropdown.style.display = 'block';
            })
            .catch(() => {
                custDropdown.style.display = 'none';
            });
    }, 250);
});

function pilihPelanggan(pelanggan) {
    document.getElementById('pelanggan_id').value = pelanggan.id;
    document.getElementById('cust-input').value = pelanggan.nama;
    document.getElementById('cust-alamat').value = pelanggan.alamat;
    document.getElementById('cust-telepon').value = pelanggan.telepon;
    document.getElementById('cust-email').value = pelanggan.email;
    custDropdown.style.display = 'none';
}

function updateHarga(select, id) {
    const option = select.options[select.selectedIndex];
    const harga = parseInt(option.getAttribute('data-harga')) || 0;
    const produk = dataProduk.find(p => p.id === parseInt(select.value));

    document.getElementById(`harga-${id}`).value = harga ? 'Rp ' + formatNumber(harga) : '';
    document.getElementById(`harga-hidden-${id}`).value = harga;
    document.getElementById(`preview-${id}`).src = produk && produk.gambar ? produk.gambar : placeholderProductImage;
    
    if (produk) {
        document.getElementById(`preview-info-${id}`).classList.add('d-none');
    } else {
        document.getElementById(`preview-info-${id}`).classList.remove('d-none');
    }
    
    hitungBaris(id);
}

function hitungBaris(id) {
    const qty = parseInt(document.querySelector(`input[name="items[${id}][jumlah]"]`).value) || 0;
    const harga = parseInt(document.getElementById(`harga-hidden-${id}`).value) || 0;
    const sub = qty * harga;

    document.getElementById(`sub-${id}`).value = sub ? 'Rp ' + formatNumber(sub) : '';
    hitungTotal();
}

function updateStockVerification() {
    const stockBody = document.getElementById('stock-verification-body');
    const warningBox = document.getElementById('stok-warning');
    const statusBadge = document.getElementById('stock-status-badge');

    const summary = {};
    let hasWarning = false;

    document.querySelectorAll('#items-tbody tr').forEach(row => {
        const select = row.querySelector('select[name^="items["]');
        const qtyInput = row.querySelector('input[type="number"]');
        if (!select || !qtyInput) return;

        const produkId = parseInt(select.value);
        const qty = parseInt(qtyInput.value) || 0;
        const produk = dataProduk.find(p => p.id === produkId);
        if (!produk || !qty) return;

        if (!summary[produkId]) {
            summary[produkId] = {produk, kebutuhan: 0};
        }
        summary[produkId].kebutuhan += qty;
    });

    if (Object.keys(summary).length === 0) {
        stockBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Pilih produk untuk melihat verifikasi stok.</td></tr>';
        warningBox.classList.add('d-none');
        statusBadge.className = 'badge rounded-pill bg-success text-white px-3';
        statusBadge.textContent = 'Semua stok cukup';
        document.getElementById('btn-simpan').disabled = false;
        return;
    }

    const rows = Object.values(summary).map(entry => {
        const tersedia = entry.produk.stok || 0;
        const kebutuhan = entry.kebutuhan;
        const selisih = tersedia - kebutuhan;
        const status = selisih >= 0 ? 'Cukup' : 'Kurang';
        if (selisih < 0) hasWarning = true;

        return `
            <tr class="${selisih < 0 ? 'table-danger' : ''}">
                <td>${entry.produk.nama}</td>
                <td>${tersedia}</td>
                <td>${kebutuhan}</td>
                <td>${selisih}</td>
                <td>${status}</td>
            </tr>
        `;
    });

    stockBody.innerHTML = rows.join('');

    const notifyButton = document.getElementById('btn-notify-stok-kurang');

    if (hasWarning) {
        warningBox.textContent = 'Beberapa item melebihi stok tersedia. PO masih bisa disimpan. Tekan tombol notifikasi untuk memberi tahu operator gudang.';
        warningBox.classList.remove('d-none');
        statusBadge.className = 'badge rounded-pill bg-danger text-white px-3';
        statusBadge.textContent = 'Stok tidak cukup';
        if (notifyButton) {
            notifyButton.classList.remove('d-none');
        }
    } else {
        warningBox.classList.add('d-none');
        statusBadge.className = 'badge rounded-pill bg-success text-white px-3';
        statusBadge.textContent = 'Semua stok cukup';
        if (notifyButton) {
            notifyButton.classList.add('d-none');
        }
    }
}

function hitungTotal() {
    let total = 0;
    let totalItens = 0;

    document.querySelectorAll('#items-tbody tr').forEach(row => {
        const hargaHidden = row.querySelector('input[id^="harga-hidden-"]');
        const qtyInput = row.querySelector('input[type="number"]');
        if (!hargaHidden || !qtyInput) return;

        const qty = parseInt(qtyInput.value) || 0;
        const harga = parseInt(hargaHidden.value) || 0;
        total += qty * harga;
        if (qty > 0) totalItens++;
    });

    document.getElementById('total-po').textContent = total ? 'Rp ' + formatNumber(total) : 'Rp 0';
    document.getElementById('total-nilai').value = total;
    document.getElementById('summary-total').textContent = total ? 'Rp ' + formatNumber(total) : 'Rp 0';
    document.getElementById('total-item').textContent = totalItens;
    updateStockVerification();
}

function tambahItem() {
    itemCount++;
    const tbody = document.getElementById('items-tbody');
    const newRow = document.createElement('tr');
    newRow.id = `item-${itemCount}`;
    newRow.innerHTML = `
        <td>${itemCount}</td>
        <td>
            <div class="product-preview">
                <img id="preview-${itemCount}" src="${placeholderProductImage}" alt="Preview Produk" class="product-thumbnail">
                <div class="product-preview-info">
                    <select name="items[${itemCount}][produk_id]" class="form-select form-select-sm" onchange="updateHarga(this, ${itemCount})" required>
                        <option value="">-- Pilih Produk --</option>
                        ${dataProduk.map(p => `<option value="${p.id}" data-harga="${p.harga}" data-stok="${p.stok}">${p.nama}</option>`).join('')}
                    </select>
                    <span id="preview-name-${itemCount}" class="product-name d-none"></span>
                    <div id="preview-info-${itemCount}" class="product-info">Pilih produk untuk melihat detail dan gambar.</div>
                </div>
            </div>
        </td>
        <td>
            <input type="number" name="items[${itemCount}][jumlah]" class="form-control form-control-sm" min="1" value="1" onchange="hitungBaris(${itemCount})" required>
        </td>
        <td>
            <input type="text" id="harga-${itemCount}" class="form-control form-control-sm mono" readonly>
            <input type="hidden" name="items[${itemCount}][harga_satuan]" id="harga-hidden-${itemCount}" value="0">
        </td>
        <td>
            <input type="text" id="sub-${itemCount}" class="form-control form-control-sm mono" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(${itemCount})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    newRow.classList.add('added');
    tbody.appendChild(newRow);
    hitungTotal();
}

function hapusItem(id) {
    const row = document.getElementById(`item-${id}`);
    const totalRows = document.querySelectorAll('#items-tbody tr').length;

    if (totalRows > 1) {
        row.remove();
        hitungTotal();
    } else {
        alert('Minimal ada 1 item pesanan');
    }
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const formPo = document.getElementById('form-po');
const sendShortageNotificationInput = document.getElementById('send-shortage-notification');
const btnSimpan = document.getElementById('btn-simpan');

if (btnSimpan) {
    btnSimpan.addEventListener('click', function() {
        if (sendShortageNotificationInput) {
            sendShortageNotificationInput.value = '0';
        }
    });
}

function getShortageDetails() {
    const details = [];
    document.querySelectorAll('#stock-verification-body tr').forEach(row => {
        const cols = row.querySelectorAll('td');
        if (cols.length < 5) return;
        const nama = cols[0].textContent.trim();
        const tersedia = parseInt(cols[1].textContent.trim()) || 0;
        const kebutuhan = parseInt(cols[2].textContent.trim()) || 0;
        const selisih = parseInt(cols[3].textContent.trim()) || 0;
        if (kebutuhan > tersedia) {
            details.push({
                nama_produk: nama,
                stok_tersedia: tersedia,
                kebutuhan: kebutuhan,
                kurang: Math.abs(selisih),
            });
        }
    });
    return details;
}

// Show custom popup alert
function showPopupAlert(title, message, type = 'success', buttons = []) {
    const overlay = document.createElement('div');
    overlay.className = 'popup-alert-overlay';

    let iconClass = '✓';
    if (type === 'error') {
        iconClass = '✕';
    } else if (type === 'info') {
        iconClass = 'ℹ';
    }

    const popup = document.createElement('div');
    popup.className = `popup-alert ${type}`;
    popup.innerHTML = `
        <div class="popup-icon">${iconClass}</div>
        <div class="popup-title">${title}</div>
        <div class="popup-message">${message}</div>
        <div class="popup-buttons" id="popup-buttons-container"></div>
    `;

    overlay.appendChild(popup);
    document.body.appendChild(overlay);

    const buttonsContainer = popup.querySelector('#popup-buttons-container');

    if (buttons.length === 0) {
        // Default close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'popup-btn popup-btn-primary';
        closeBtn.innerHTML = '<i class="fas fa-check"></i>Tutup';
        closeBtn.addEventListener('click', () => closePopup());
        buttonsContainer.appendChild(closeBtn);
    } else {
        buttons.forEach((btn) => {
            const button = document.createElement('button');
            button.className = `popup-btn ${btn.class}`;
            button.innerHTML = btn.icon ? `<i class="${btn.icon}"></i>${btn.text}` : btn.text;
            button.addEventListener('click', () => {
                if (btn.callback) btn.callback();
                closePopup();
            });
            buttonsContainer.appendChild(button);
        });
    }

    function closePopup() {
        popup.classList.add('closing');
        overlay.classList.add('closing');
        setTimeout(() => {
            document.body.removeChild(overlay);
        }, 400);
    }

    // Close on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closePopup();
        }
    });
}

async function notifyStockShortage() {
    const details = getShortageDetails();
    if (!details.length) {
        showPopupAlert(
            'Tidak Ada Kekurangan',
            'Tidak ada kekurangan stok yang terdeteksi.',
            'info'
        );
        return;
    }

    const nomorPo = document.getElementById('nomor_po') ? document.getElementById('nomor_po').value : null;
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

    try {
        const res = await fetch('{{ route('notifikasi.stokKurangDraft') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nomor_po: nomorPo, detail_kurang: details })
        });

        const json = await res.json();
        if (res.ok && json.success) {
            showPopupAlert(
                'Berhasil!',
                'Notifikasi stok kurang berhasil dikirim ke operator gudang.',
                'success',
                [{
                    text: 'OK',
                    class: 'popup-btn-success',
                    icon: 'fas fa-check',
                    callback: () => {
                        // hide the notify button to prevent duplicates
                        const btn = document.getElementById('btn-notify-stok-kurang');
                        if (btn) btn.classList.add('d-none');
                    }
                }]
            );
        } else {
            console.error(json);
            showPopupAlert(
                'Gagal Mengirim',
                'Gagal mengirim notifikasi. Cek konsol untuk detail.',
                'error'
            );
        }
    } catch (err) {
        console.error(err);
        showPopupAlert(
            'Terjadi Kesalahan',
            'Terjadi kesalahan saat mengirim notifikasi. Silakan coba lagi.',
            'error'
        );
    }
}

formPo.addEventListener('submit', function(e) {
    const pelangganId = document.getElementById('pelanggan_id').value;
    if (!pelangganId) {
        e.preventDefault();
        alert('Pilih pelanggan terlebih dahulu');
        return;
    }

    const btn = document.getElementById('btn-simpan');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    }
});
</script>
@endpush
@endsection