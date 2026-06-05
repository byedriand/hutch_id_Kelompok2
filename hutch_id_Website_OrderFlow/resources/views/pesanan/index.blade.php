@extends('layouts.app')

@section('content')
<div>
    @push('styles')
    <style>
        /* ===== SIDEBAR CONSISTENCY FIX ===== */
        #sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1030;
        }

        .sidebar-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .row {
            align-items: stretch;
        }

        /* ===== MODERN FILTER STYLING ===== */
        
        /* Main Filter Wrapper - Premium Glass Effect */
        .card-filter-wrapper {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, hsla(214, 100%, 99%, 0.90) 100%);
            backdrop-filter: blur(20px);
            border: 1.5px solid rgba(45, 125, 210, 0.2);
            border-radius: 1.75rem;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset,
                0 0 30px rgba(45, 125, 210, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            animation: filterGlassIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }

        /* Page Header Styling - Consistent with Other Menus */
        .page-header.custom-pesanan {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            gap: 1.5rem;
            flex-wrap: wrap;
            animation: slideDown 0.6s ease-out;
            padding: 0;
            border-bottom: none;
        }

        .page-header.custom-pesanan > div:first-child {
            flex: 1;
        }

        .page-header.custom-pesanan h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.25rem;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .page-header.custom-pesanan h1::before {
            content: '';
            display: inline-flex;
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #0066cc, #0052a3);
            border-radius: 2px;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
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

        .page-header.custom-pesanan p {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            margin: 0;
        }

        .top-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .top-actions .btn-primary {
            background: linear-gradient(135deg, #0066cc, #0052a3);
            border: none;
            border-radius: 0.9rem;
            padding: 0.75rem 1.75rem;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 26px rgba(0, 82, 163, 0.28);
            color: white;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .top-actions .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .top-actions .btn-primary:hover::before {
            left: 100%;
        }

        .top-actions .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px rgba(0, 82, 163, 0.38);
        }

        .top-actions .btn-primary:active {
            transform: translateY(-1px);
        }

        .top-actions .btn-primary i {
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        .top-actions .btn-primary:hover i {
            transform: rotate(90deg) scale(1.1);
        }

        /* Card Filter Wrapper Animation */
        .card-filter-wrapper {
            animation: filterGlassIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .card-filter-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        @keyframes filterGlassIn {
            from {
                opacity: 0;
                transform: translateY(-15px) scale(0.95);
                filter: blur(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        .filter-header h6 {
            margin: 0;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .filter-header h6::after {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #2563eb, #10b981);
            border-radius: 50%;
            animation: headerPulse 2.5s ease-in-out infinite;
        }

        @keyframes headerPulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
        }

        /* Filter Row - Grid Layout */
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            align-items: flex-end;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* Filter Group - Individual Input Group */
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .filter-group label {
            font-weight: 700;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 0.85rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Modern Input Fields */
        .filter-group .form-control,
        .filter-group .form-select {
            border-radius: 0.9rem;
            border: 1.5px solid rgba(45, 125, 210, 0.15);
            padding: 0.75rem 1.1rem;
            font-size: 0.95rem;
            background: linear-gradient(135deg, rgba(248, 251, 255, 0.6) 0%, rgba(240, 244, 255, 0.4) 100%);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-weight: 500;
            color: #1e293b;
        }

        .filter-group .form-control::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .filter-group .form-control:hover,
        .filter-group .form-select:hover {
            border-color: rgba(45, 125, 210, 0.3);
            background: linear-gradient(135deg, rgba(248, 251, 255, 0.8) 0%, rgba(240, 244, 255, 0.6) 100%);
            box-shadow: 0 4px 12px rgba(45, 125, 210, 0.06);
        }

        .filter-group .form-control:focus,
        .filter-group .form-select:focus {
            border-color: #2563eb;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 
                0 0 0 4px rgba(59, 130, 246, 0.15),
                0 8px 20px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        /* Input Group Currency */
        .filter-group .input-group .input-group-text {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(45, 125, 210, 0.05) 100%);
            border: 1.5px solid rgba(45, 125, 210, 0.15);
            border-right: 0;
            color: #2563eb;
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 0.9rem 0 0 0.9rem;
        }

        .filter-group .input-group .form-control {
            border-radius: 0 0.9rem 0.9rem 0;
            border-left: 0;
        }

        /* Filter Actions - Button Group */
        .filter-actions {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .filter-actions .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            border-radius: 0.9rem;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .filter-actions .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.3) 0%, transparent 100%);
            transform: scale(0);
            transition: transform 0.6s ease;
            pointer-events: none;
        }

        .filter-actions .btn:active::before {
            transform: scale(2);
        }

        /* Primary Button - Advanced Filter */
        .filter-actions .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
            color: #ffffff;
        }

        .filter-actions .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.35);
        }

        .filter-actions .btn-primary:active {
            transform: translateY(-1px);
        }

        /* Success Button - Apply Filter */
        .filter-actions .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
            color: #ffffff;
        }

        .filter-actions .btn-success:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.35);
        }

        .filter-actions .btn-success:active {
            transform: translateY(-1px);
        }

        /* Secondary Button - Reset */
        .filter-actions .btn-outline-secondary {
            border: 2px solid rgba(45, 125, 210, 0.3);
            color: #2563eb;
            background: rgba(255, 255, 255, 0.8);
            font-weight: 700;
        }

        .filter-actions .btn-outline-secondary:hover {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.05) 0%, rgba(37, 99, 235, 0.05) 100%);
            border-color: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(45, 125, 210, 0.15);
        }

        /* Advanced Filters Collapse */
        .advanced-filters-collapse {
            margin-top: 1.5rem;
            animation: advancedSlideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            z-index: 1;
        }

        @keyframes advancedSlideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
                max-height: 0;
            }
            to {
                opacity: 1;
                transform: translateY(0);
                max-height: 1000px;
            }
        }

        /* Advanced Filters Body - Premium Container */
        .advanced-filters-body {
            background: linear-gradient(135deg, 
                rgba(219, 234, 254, 0.3) 0%, 
                rgba(240, 244, 255, 0.3) 100%);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(45, 125, 210, 0.2);
            border-radius: 1.25rem;
            padding: 1.75rem;
            box-shadow: 
                0 8px 24px rgba(45, 125, 210, 0.08),
                inset 0 1px 2px rgba(255, 255, 255, 0.5);
        }

        .advanced-filters-body .filter-group {
            padding: 0;
        }

        .advanced-filters-label {
            font-weight: 800;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.7rem;
            display: block;
        }

        /* Checkbox Wrapper - Modern Toggle */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1.15rem;
            background: linear-gradient(135deg, rgba(248, 251, 255, 0.6) 0%, rgba(240, 244, 255, 0.4) 100%);
            border: 1.5px solid rgba(45, 125, 210, 0.15);
            border-radius: 0.9rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            height: fit-content;
        }

        .checkbox-wrapper:hover {
            border-color: rgba(45, 125, 210, 0.3);
            background: linear-gradient(135deg, rgba(248, 251, 255, 0.8) 0%, rgba(240, 244, 255, 0.6) 100%);
            box-shadow: 0 4px 12px rgba(45, 125, 210, 0.1);
        }

        .checkbox-wrapper input[type="checkbox"] {
            cursor: pointer;
            width: 1.2rem;
            height: 1.2rem;
            accent-color: #2563eb;
            transition: all 0.2s ease;
        }

        .checkbox-wrapper input[type="checkbox"]:checked {
            transform: scale(1.1);
        }

        .checkbox-wrapper label {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .checkbox-wrapper:hover label {
            color: #2563eb;
        }

        /* Active Filter Badges */
        .active-filter-section {
            display: flex;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .active-filter-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(219, 234, 254, 0.5) 0%, rgba(219, 234, 254, 0.3) 100%);
            padding: 0.5rem 0.95rem;
            border-radius: 0.9rem;
            border: 1.5px solid rgba(37, 99, 235, 0.2);
        }

        .active-filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.95rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .active-filter-badge:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.15);
        }

        .active-filter-badge-search {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #0c4a6e;
        }

        .active-filter-badge-status {
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
            color: #0c4a6e;
        }

        .active-filter-badge-date {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            color: #ffffff;
        }

        .active-filter-badge i {
            font-size: 0.85rem;
        }

        /* Layout tweaks for pesanan list */
        .pesanan-grid { gap: 1.25rem; }

        .order-card {
            width: 100%;
            min-height: auto;
            border-radius: 1.5rem;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 12px 40px rgba(45, 125, 210, 0.12), inset 0 1.5px 2px rgba(255, 255, 255, 0.7);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(45, 125, 210, 0.15);
            padding: 0;
            position: relative;
            overflow: visible;
            animation: cardFadeInUp 0.6s ease-out both;
            display: flex;
            flex-direction: column;
        }

        .order-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(45, 125, 210, 0.08), transparent 50%);
            pointer-events: none;
        }

        .order-card:hover {
            transform: translateY(-12px) scale(1.01);
            box-shadow: 0 24px 56px rgba(45, 125, 210, 0.2), inset 0 1.5px 2px rgba(255, 255, 255, 0.7);
            border-color: rgba(45, 125, 210, 0.3);
        }

        /* Card Header Section */
        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid rgba(45, 125, 210, 0.12);
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, rgba(255,255,255,0.6) 0%, rgba(248,251,255,0.5) 100%);
            backdrop-filter: blur(4px);
            gap: 0.75rem;
        }
        
        .order-card-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #2563eb 0%, #3b82f6 100%);
            border-radius: 1.5rem 0 0 0;
        }

        .order-card-header-left {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            flex: 1;
        }

        .order-card-po-info {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        }

        .order-po-number {
            font-size: 0.7rem;
            font-weight: 800;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            padding: 0.4rem 0.85rem;
            border-radius: 0.65rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            width: fit-content;
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.15);
            border: 1.5px solid rgba(30, 64, 175, 0.2);
        }
        
        .order-po-number::before {
            content: '📋';
            font-size: 0.8rem;
        }

        .order-po-date {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            white-space: nowrap;
        }
        
        .order-po-date::before {
            content: '📅';
            font-size: 0.8rem;
        }

        .order-card-header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }

        /* Card Main Content */
        .order-card-content {
            padding: 1.1rem 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
            gap: 0.6rem;
        }

        .order-card-main {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1.5rem;
            align-items: flex-start;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(45, 125, 210, 0.08);
            position: relative;
            z-index: 2;
        }

        .order-card-customer-info {
            flex: 1;
            min-width: 0;
        }

        .order-card-customer-name {
            font-size: 1.15rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-card-product-desc {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0;
            line-height: 1.3;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .order-card-product-desc::before {
            content: '📦';
            font-size: 0.95rem;
        }

        .order-card-right-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.4rem;
            text-align: right;
            min-width: fit-content;
        }
        
        .order-card-right-info > div:first-child {
            text-align: center;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            padding: 0.45rem 0.9rem;
            border-radius: 0.7rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
        }
        
        .order-card-right-info > div:nth-child(2) {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            justify-content: flex-end;
        }
        
        .order-card-right-info > div:nth-child(2)::before {
            content: '🗒';
            font-size: 0.8rem;
        }

        /* Info Grid Section */
        .order-card-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 0.9rem;
            padding: 1.1rem;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(45, 125, 210, 0.05) 100%);
            border-radius: 1.1rem;
            border: 2px solid rgba(45, 125, 210, 0.15);
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 16px rgba(45, 125, 210, 0.06), inset 0 1px 1px rgba(255, 255, 255, 0.3);
        }

        .order-stat-item {
            text-align: center;
            padding: 0.65rem;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 0.9rem;
            border: 1.5px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .order-stat-item:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 125, 210, 0.1);
            border-color: rgba(45, 125, 210, 0.2);
        }

        .order-stat-label {
            font-size: 0.6rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 800;
            margin-bottom: 0.35rem;
            display: block;
        }

        .order-stat-value {
            font-size: 1rem;
            font-weight: 900;
            color: #1e293b;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card Footer */
        .order-card-footer {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            border-top: 2px solid rgba(45, 125, 210, 0.1);
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, rgba(248,251,255,0.4) 0%, rgba(248,251,255,0.2) 100%);
        }

        .order-card-footer-text {
            font-size: 0.8rem;
            color: #64748b;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
        }
        
        .order-card-footer-text::before {
            content: 'ℹ';
            font-weight: bold;
            color: #2563eb;
            font-size: 0.9rem;
        }

        .order-card-footer-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
            padding: 0.5rem 1rem;
        }

        .order-card-footer-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.5);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .order-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.6rem 1.1rem;
            border-radius: 1rem;
            display: inline-block;
            transition: all 0.3s ease;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
        }

        .order-badge.b-wait {
            background: linear-gradient(135deg, #FFC107 0%, #FFB300 100%);
            color: #FFFFFF;
            border-color: rgba(255, 193, 7, 0.4);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .order-badge.b-conf {
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
            color: #0c4a6e;
            border-color: rgba(12, 74, 110, 0.3);
            box-shadow: 0 4px 12px rgba(12, 74, 110, 0.2);
        }

        .order-badge.b-prod {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .order-badge.b-ready {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
            animation: badgePulse 2s ease-in-out infinite;
        }

        .order-badge.b-done {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 6px 16px rgba(29, 78, 216, 0.35);
            animation: badgePulse 2s ease-in-out infinite;
        }

        .order-badge.b-cancel {
            background: linear-gradient(135deg, #fca5a5 0%, #f87171 100%);
            color: #7f1d1d;
            border-color: rgba(127, 29, 29, 0.3);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }

        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35); }
            50% { box-shadow: 0 8px 20px rgba(37, 99, 235, 0.5); }
        }

        .order-badge:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .order-amount {
            font-size: 0.95rem;
            font-weight: 900;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 10;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        /* Constraint header columns - removed as using new structure */

        .page-header.custom-pesanan {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 1.5rem;
            border: 1.5px solid rgba(45, 125, 210, 0.15);
            box-shadow: 0 8px 24px rgba(45, 125, 210, 0.08), inset 0 1px 1px rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            animation: headerFadeIn 0.5s ease-out;
        }

        @keyframes headerFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header.custom-pesanan h1 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #2d7dd2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .page-header.custom-pesanan p {
            color: #64748b;
            font-size: 0.95rem;
            margin: 0.5rem 0 0;
            font-weight: 500;
        }

        .page-header.custom-pesanan .top-actions {
            display: flex;
            gap: 0.75rem;
        }

        .page-header.custom-pesanan .top-actions .btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: all 0.3s ease;
            font-weight: 700;
        }

        .page-header.custom-pesanan .top-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .page-header.custom-pesanan h1 {
                font-size: 1.3rem;
            }

            .page-header.custom-pesanan h1::before {
                height: 18px;
            }

            .top-actions .btn-primary {
                padding: 0.7rem 1.5rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width:991px){
            .page-header.custom-pesanan {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 1.5rem;
            }

            .page-header.custom-pesanan h1 {
                font-size: 1.2rem;
            }

            .page-header.custom-pesanan h1::before {
                height: 16px;
            }

            .page-header.custom-pesanan p {
                font-size: 0.8rem;
            }

            .top-actions {
                width: 100%;
            }

            .top-actions .btn-primary {
                flex: 1;
                font-size: 0.8rem;
                padding: 0.65rem 1.2rem;
                justify-content: center;
            }

            .order-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .order-card-po-info {
                flex-direction: row;
                align-items: center;
                gap: 0.8rem;
                width: 100%;
                justify-content: space-between;
            }

            .order-card-header-right {
                align-items: flex-start;
                width: 100%;
            }

            .order-card-main {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .order-card-right-info {
                align-items: flex-start;
                width: 100%;
            }

            .order-card-footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-card-footer-btn {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .order-card {
                border-radius: 1.25rem;
            }

            .order-card-header {
                padding: 1.25rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .order-card-po-info {
                flex-direction: row;
                align-items: center;
                gap: 0.8rem;
                width: 100%;
            }

            .order-card-content {
                padding: 1.25rem;
            }

            .order-card-footer {
                padding: 1rem 1.25rem;
            }

            .order-card-customer-name {
                font-size: 1.1rem;
            }

            .order-amount {
                font-size: 1.1rem;
            }

            .page-header.custom-pesanan {
                padding: 1.5rem;
                gap: 1rem;
            }

            .page-header.custom-pesanan h1 {
                font-size: 1.3rem;
            }

            .order-card-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                padding: 1rem;
            }

            .masonry-item, .masonry-sizer {
                width: 100% !important;
            }
        }

        @media (max-width: 480px) {
            .page-header.custom-pesanan {
                margin-bottom: 1rem;
            }

            .page-header.custom-pesanan h1 {
                font-size: 1rem;
            }

            .page-header.custom-pesanan h1::before {
                width: 3px;
                height: 14px;
            }

            .page-header.custom-pesanan p {
                font-size: 0.75rem;
            }

            .top-actions {
                width: 100%;
                gap: 0.5rem;
            }

            .top-actions .btn-primary {
                flex: 1;
                font-size: 0.75rem;
                padding: 0.6rem 0.8rem;
                min-width: auto;
            }

            .top-actions .btn-primary i {
                font-size: 0.75rem;
            }

            .order-card-po-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .order-po-number {
                font-size: 0.65rem;
                padding: 0.35rem 0.75rem;
            }

            .order-po-date {
                font-size: 0.7rem;
            }

            .order-amount {
                font-size: 0.85rem;
            }
        }
        .pagination .page-link { border-radius: 10px; padding: 0.4rem 0.7rem; color: #0b3d7f; }
        .pagination .page-item.active .page-link { background: #2d7dd2; border-color: #2d7dd2; color: #fff; }

        /* Small utilities */
        .order-card .order-foot .btn { min-width: 140px; }

        /* Filter input icon tweaks */
        .input-group-text.bg-white { border-radius: 12px 0 0 12px; border-right: 0; }
        .form-control.form-control-sm { border-radius: 0 12px 12px 0; }
        .form-select.form-select-sm { border-radius: 0 12px 12px 0; }

        /* Masonry layout (JS) - items set to percentage width */
        .masonry { position: relative; }
        .masonry-item {
            width: 48%;
            margin-bottom: 1.5rem;
            display: block;
            opacity: 0;
            transform: translateY(24px) scale(0.92);
            transition: opacity 600ms cubic-bezier(.2, .8, .2, 1), transform 600ms cubic-bezier(.2, .8, .2, 1);
        }

        .masonry-item:nth-child(2) { transition-delay: 60ms; }
        .masonry-item:nth-child(3) { transition-delay: 120ms; }
        .masonry-item:nth-child(4) { transition-delay: 180ms; }
        .masonry-item:nth-child(5) { transition-delay: 240ms; }
        .masonry-sizer { width: 48%; }

        @media (max-width: 1199px) { .masonry-item, .masonry-sizer { width: 48%; } }
        @media (max-width: 991px) { .masonry-item, .masonry-sizer { width: 100%; } }

        /* Reveal state for staggered animation */
        .masonry-item.show { 
            opacity: 1; 
            transform: none; 
        }

        /* Skeleton loader styles */
        .skeleton-wrapper { display: block; }
        .skeleton-card { background: #fff; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 12px 24px rgba(15,64,124,0.05); border:1px solid rgba(219,229,241,0.9); }
        .s-line { height: 12px; background: linear-gradient(90deg,#f3f4f6 25%,#e6eefb 50%,#f3f4f6 75%); background-size: 200% 100%; border-radius: 6px; animation: shimmer 1.2s linear infinite; }
        .s-title { width: 55%; height: 18px; margin-bottom: 0.6rem; }
        .s-sub { width: 40%; height: 12px; margin-bottom: 0.5rem; }
        .s-row { display:flex; gap:0.8rem; margin-top:0.6rem; }
        .s-col { flex:1; }

        @keyframes shimmer { from { background-position: 200% 0 } to { background-position: -200% 0 } }

        /* Responsive Filter */
        @media (max-width: 1199px) {
            .filter-row {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 0.95rem;
            }

            .filter-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .filter-actions .btn {
                flex: 0 1 auto;
                min-width: 110px;
            }

            .active-filter-section {
                width: 100%;
            }
        }

        @media (max-width: 991px) {
            .card-filter-wrapper {
                padding: 1.5rem;
                border-radius: 1.5rem;
            }

            .filter-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.9rem;
            }

            .filter-header {
                margin-bottom: 1.25rem;
            }

            .active-filter-section {
                width: 100%;
                margin-bottom: 1rem;
            }

            .filter-actions {
                flex-direction: row;
                width: auto;
                gap: 0.7rem;
            }

            .filter-actions .btn {
                padding: 0.65rem 1.2rem;
                font-size: 0.85rem;
            }

            .advanced-filters-body {
                padding: 1.5rem;
            }

            .advanced-filters-body .filter-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 767px) {
            .card-filter-wrapper {
                padding: 1.2rem;
                border-radius: 1.25rem;
                margin-bottom: 1.5rem;
            }

            .filter-header {
                margin-bottom: 1rem;
                padding-bottom: 0.8rem;
            }

            .filter-header h6 {
                font-size: 0.95rem;
            }

            .filter-row {
                grid-template-columns: 1fr;
                gap: 0.7rem;
            }

            .filter-group label {
                font-size: 0.8rem;
            }

            .filter-group .form-control,
            .filter-group .form-select {
                padding: 0.65rem 0.85rem;
                font-size: 0.9rem;
            }

            .filter-actions {
                flex-direction: column;
                width: 100%;
                gap: 0.6rem;
            }

            .filter-actions .btn {
                width: 100%;
                padding: 0.7rem 1rem;
                font-size: 0.85rem;
                justify-content: center;
            }

            .advanced-filters-body {
                padding: 1rem;
                border-radius: 1rem;
            }

            .advanced-filters-body .filter-row {
                grid-template-columns: 1fr;
                gap: 0.7rem;
            }

            .advanced-filters-label {
                font-size: 0.75rem;
                margin-bottom: 0.6rem;
            }

            .checkbox-wrapper {
                padding: 0.7rem 0.95rem;
                gap: 0.6rem;
            }

            .checkbox-wrapper input[type="checkbox"] {
                width: 1.1rem;
                height: 1.1rem;
            }

            .checkbox-wrapper label {
                font-size: 0.9rem;
            }

            .active-filter-section {
                width: 100%;
                gap: 0.5rem;
            }

            .active-filter-label {
                padding: 0.4rem 0.8rem;
                font-size: 0.7rem;
                gap: 0.4rem;
            }

            .active-filter-badge {
                padding: 0.4rem 0.8rem;
                font-size: 0.7rem;
                gap: 0.4rem;
            }
        }

        @media (max-width: 480px) {
            .card-filter-wrapper {
                padding: 1rem;
                border-radius: 1rem;
            }

            .filter-row {
                gap: 0.6rem;
            }

            .filter-group .form-control,
            .filter-group .form-select {
                padding: 0.6rem 0.75rem;
                font-size: 0.85rem;
            }

            .filter-actions .btn {
                padding: 0.65rem 0.9rem;
                font-size: 0.8rem;
                gap: 0.4rem;
            }

            .filter-actions .btn i {
                display: none;
            }

            .advanced-filters-body {
                padding: 0.9rem;
            }

            .active-filter-badge {
                padding: 0.35rem 0.7rem;
                font-size: 0.65rem;
            }
        }
    </style>
    @endpush
    <div class="page-header custom-pesanan">
        <div>
            <h1 class="h3">Daftar Pesanan</h1>
            <p class="mb-0">Lihat ringkasan dan status semua Purchase Order secara cepat.</p>
        </div>
        <div class="top-actions">
            @if(auth()->user()->role !== 'operator_gudang')
                <a href="{{ route('pesanan.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Buat PO Baru
                </a>
            @endif
        </div>
    </div>

    <div class="card-filter-wrapper">
        <form id="filter-form" method="GET" action="{{ route('pesanan.index') }}">
            <div class="filter-header">
                <h6>
                    <i class="fas fa-filter"></i>
                    Filter & Pencarian
                </h6>
            </div>

            <!-- Active Filters Indicator -->
            @php
                $hasActiveFilters = request()->filled('cari') || request()->filled('status') || 
                                   request()->filled('dari') || request()->filled('sampai') ||
                                   request()->filled('min_total') || request()->filled('max_total') ||
                                   request()->filled('produk') || request()->filled('multi_item');
            @endphp
            @if($hasActiveFilters)
                <div style="display: flex; gap: 0.6rem; margin-bottom: 1.25rem; flex-wrap: wrap; align-items: center; position: relative; z-index: 1;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fas fa-check-circle"></i>Filter Aktif:
                    </span>
                    @if(request()->filled('cari'))
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #0c4a6e; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(12, 74, 110, 0.2); box-shadow: 0 2px 8px rgba(30, 64, 175, 0.1);">
                            <i class="fas fa-search"></i>{{ request('cari') }}
                        </span>
                    @endif
                    @if(request()->filled('status'))
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%); color: #0c4a6e; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(12, 74, 110, 0.2); box-shadow: 0 2px 8px rgba(30, 64, 175, 0.1);">
                            <i class="fas fa-flag"></i>{{ ucwords(str_replace('_', ' ', request('status'))) }}
                        </span>
                    @endif
                    @if(request()->filled('dari'))
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); color: #ffffff; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);">
                            <i class="fas fa-calendar"></i>Dari {{ request('dari') }}
                        </span>
                    @endif
                    @if(request()->filled('sampai'))
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); color: #ffffff; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);">
                            <i class="fas fa-calendar"></i>Sampai {{ request('sampai') }}
                        </span>
                    @endif
                </div>
            @endif

            <!-- Basic Filters Row -->
            <div class="filter-row">
                <div class="filter-group">
                    <label for="cari"><i class="fas fa-search" style="margin-right: 0.3rem;"></i>Cari Keyword</label>
                    <input type="text" id="cari" name="cari" class="form-control" 
                           placeholder="PO, Pelanggan, Produk..." value="{{ request('cari') }}">
                </div>
                <div class="filter-group">
                    <label for="status"><i class="fas fa-tag" style="margin-right: 0.3rem;"></i>Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">📋 Semua Status</option>
                        <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>✅ Dikonfirmasi</option>
                        <option value="dalam_produksi" {{ request('status') == 'dalam_produksi' ? 'selected' : '' }}>⚙️ Dalam Produksi</option>
                        <option value="siap_kirim" {{ request('status') == 'siap_kirim' ? 'selected' : '' }}>📦 Siap Kirim</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>🎉 Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="dari"><i class="fas fa-calendar-alt" style="margin-right: 0.3rem;"></i>Dari</label>
                    <input type="date" id="dari" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="filter-group">
                    <label for="sampai"><i class="fas fa-calendar-check" style="margin-right: 0.3rem;"></i>Sampai</label>
                    <input type="date" id="sampai" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
            </div>

            <!-- Action Buttons & Advanced Toggle -->
            <div style="display: flex; flex-wrap: wrap; gap: 1.2rem; align-items: center; justify-content: space-between; margin-bottom: 1rem; position: relative; z-index: 1;">
                <div class="filter-actions">
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#advancedFilters" aria-expanded="false">
                        <i class="fas fa-sliders-h"></i>Filter Lanjutan
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i>Terapkan
                    </button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i>Reset
                    </a>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="collapse advanced-filters-collapse" id="advancedFilters">
                <div class="advanced-filters-body">
                    <div class="filter-row">
                        <div class="filter-group">
                            <span class="advanced-filters-label">💰 Nilai Minimum (Rp)</span>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="min_total" class="form-control" 
                                       placeholder="0" value="{{ request('min_total') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <span class="advanced-filters-label">💰 Nilai Maksimum (Rp)</span>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="max_total" class="form-control" 
                                       placeholder="0" value="{{ request('max_total') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label for="produk"><i class="fas fa-box" style="margin-right: 0.3rem;"></i>Nama Produk</label>
                            <input type="text" id="produk" name="produk" class="form-control" 
                                   placeholder="Cari nama produk..." value="{{ request('produk') }}">
                        </div>
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <div class="checkbox-wrapper">
                                <input class="form-check-input" type="checkbox" name="multi_item" 
                                       id="multi_item" {{ request('multi_item') ? 'checked' : '' }}>
                                <label class="form-check-label" for="multi_item">
                                    📊 Hanya Multi-Item
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Badge -->
    @if(request()->filled('cari') || request()->filled('status') || request()->filled('dari') || request()->filled('sampai') || request()->filled('min_total') || request()->filled('max_total') || request()->filled('produk') || request()->filled('multi_item'))
        <div class="mb-3 d-flex flex-wrap gap-2">
            @if(request()->filled('cari'))
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Cari: {{ request('cari') }}</span>
            @endif
            @if(request()->filled('status'))
                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">Status: {{ str_replace('_', ' ', request('status')) }}</span>
            @endif
            @if(request()->filled('dari'))
                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Dari: {{ request('dari') }}</span>
            @endif
            @if(request()->filled('sampai'))
                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">Sampai: {{ request('sampai') }}</span>
            @endif
            @if(request()->filled('min_total'))
                <span class="badge rounded-pill bg-info bg-opacity-10 text-info">Min: Rp {{ number_format(request('min_total'), 0, ',', '.') }}</span>
            @endif
            @if(request()->filled('max_total'))
                <span class="badge rounded-pill bg-info bg-opacity-10 text-info">Max: Rp {{ number_format(request('max_total'), 0, ',', '.') }}</span>
            @endif
            @if(request()->filled('produk'))
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Produk: {{ request('produk') }}</span>
            @endif
            @if(request()->filled('multi_item'))
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Multi-item</span>
            @endif
            <a href="{{ route('pesanan.index') }}" class="badge rounded-pill bg-danger bg-opacity-10 text-danger text-decoration-none">Reset Filter</a>
        </div>
    @endif

    <!-- Skeleton shown while page 'loads' briefly -->
    <div id="skeleton" class="skeleton-wrapper">
        <div class="masonry">
            @for($i=0;$i<4;$i++)
            <div class="skeleton-card">
                <div class="s-line s-title"></div>
                <div class="s-line s-sub"></div>
                <div class="s-row">
                    <div class="s-col"><div class="s-line" style="height:10px;width:80%"></div></div>
                    <div class="s-col"><div class="s-line" style="height:10px;width:60%"></div></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <div id="pesananList" class="masonry pesanan-grid">
        <div class="masonry-sizer"></div>
        @forelse($pesanan as $po)
            @php
                $firstItem = optional($po->detailPesanan->first());
                $badgeMap = [
                    'menunggu_konfirmasi' => ['class' => 'b-wait', 'text' => 'Menunggu'],
                    'dikonfirmasi' => ['class' => 'b-conf', 'text' => 'Dikonfirmasi'],
                    'dalam_produksi' => ['class' => 'b-prod', 'text' => 'Dalam Produksi'],
                    'siap_kirim' => ['class' => 'b-ready', 'text' => 'Siap Kirim'],
                    'selesai' => ['class' => 'b-done', 'text' => 'Selesai'],
                    'dibatalkan' => ['class' => 'b-cancel', 'text' => 'Dibatalkan'],
                ];
                $badge = $badgeMap[$po->status] ?? ['class' => 'b-ok', 'text' => ucfirst(str_replace('_', ' ', $po->status))];
            @endphp
            <div class="masonry-item">
                <div class="order-card">
                    <!-- Card Header -->
                    <div class="order-card-header">
                        <div class="order-card-header-left">
                            <div class="order-card-po-info">
                                <div class="order-po-number">{{ $po->nomor_po }}</div>
                                <div class="order-po-date">{{ $po->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="order-card-header-right">
                            <span class="badge order-badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">Tgl Kirim: {{ $po->tanggal_pengiriman->format('d M Y') }}</div>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="order-card-content">
                        <div class="order-card-main">
                            <div class="order-card-customer-info">
                                <div class="order-card-customer-name">{{ $po->pelanggan->nama ?? 'Pelanggan N/A' }}</div>
                                <div class="order-card-product-desc">
                                    {{ $firstItem && $firstItem->produk ? $firstItem->produk->nama . ' (' . $firstItem->jumlah . ' pcs)' : '-' }}
                                    @if($po->detailPesanan->count() > 1)
                                        <br><span style="font-size: 0.8rem; color: #64748b;">+{{ $po->detailPesanan->count() - 1 }} item lainnya</span>
                                    @endif
                                </div>
                                @if(!empty($po->shortage_total) && $po->shortage_total > 0)
                                    <span class="badge bg-danger ms-0 mt-2" style="font-size:0.75rem;">⚠ Kurang: {{ $po->shortage_total }} unit</span>
                                @endif
                            </div>
                            <div class="order-card-right-info">
                                <div style="font-size: 1.2rem; font-weight: 900; color: #ffffff; letter-spacing: -0.01em; white-space: nowrap; position: relative; z-index: 10;">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <!-- Stats Section -->
                        <div class="order-card-stats">
                            <div class="order-stat-item">
                                <div class="order-stat-label">Produk Utama</div>
                                <div class="order-stat-value">{{ $firstItem && $firstItem->produk ? substr($firstItem->produk->nama, 0, 12) . (strlen($firstItem->produk->nama) > 12 ? '...' : '') : '-' }}</div>
                            </div>
                            <div class="order-stat-item">
                                <div class="order-stat-label">Total Item</div>
                                <div class="order-stat-value">{{ $po->detailPesanan->sum('jumlah') }} pcs</div>
                            </div>
                            <div class="order-stat-item">
                                <div class="order-stat-label">Jumlah PO</div>
                                <div class="order-stat-value">{{ $po->detailPesanan->count() }} Produk</div>
                            </div>
                            <div class="order-stat-item">
                                <div class="order-stat-label">Pelanggan</div>
                                <div class="order-stat-value">{{ substr($po->pelanggan->nama ?? '-', 0, 12) }}{{ strlen($po->pelanggan->nama ?? '-') > 12 ? '...' : '' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="order-card-footer">
                        <div class="order-card-footer-text">
                            <i class="fas fa-info-circle me-1"></i>Klik tombol untuk melihat detail lengkap pesanan
                        </div>
                        <a href="{{ route('pesanan.show', $po) }}" class="btn btn-sm btn-primary order-card-footer-btn">
                            <i class="fas fa-eye me-1"></i>Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="masonry-item">
                <div class="card rounded-4 shadow-sm border-0 p-5 text-center text-muted">
                    <i class="fas fa-folder-open fa-2x mb-3"></i>
                    <p class="mb-0">Tidak ada pesanan ditemukan</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 flex-column flex-md-row gap-3">
        <small class="text-muted">
            Menampilkan {{ $pesanan->firstItem() ?? 0 }}&ndash;{{ $pesanan->lastItem() ?? 0 }} dari {{ $pesanan->total() }} pesanan
        </small>
        <nav>
            {{ $pesanan->withQueryString()->links() }}
        </nav>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filter-form');
        const statusFilter = document.getElementById('status-filter');
        const dateInputs = form.querySelectorAll('input[type="date"]');

        if (statusFilter) {
            statusFilter.addEventListener('change', function () {
                form.submit();
            });
        }

        dateInputs.forEach(input => {
            input.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
<script>
    // Simple skeleton toggle: show skeleton for a short moment then reveal content
    document.addEventListener('DOMContentLoaded', function () {
        const skeleton = document.getElementById('skeleton');
        const list = document.getElementById('pesananList');
        // show skeleton at first, then replace quickly
        setTimeout(() => {
            if (skeleton) skeleton.style.display = 'none';
            if (list) {
                list.style.display = '';
                // initialize masonry after revealing content
                if (typeof imagesLoaded !== 'undefined' && typeof Masonry !== 'undefined') {
                    imagesLoaded(list, function() {
                        var msnry = new Masonry(list, {
                            itemSelector: '.masonry-item',
                            columnWidth: '.masonry-sizer',
                            percentPosition: true,
                            gutter: 16
                        });
                        // staggered reveal after layout
                        var items = list.querySelectorAll('.masonry-item');
                        items.forEach(function(it, idx){
                            setTimeout(function(){ it.classList.add('show'); msnry.layout(); }, idx * 80);
                        });
                    });
                }
            }
        }, 350);
    });
</script>
<!-- Load imagesLoaded and Masonry from CDN for better masonry layout -->
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
@endpush
@endsection
