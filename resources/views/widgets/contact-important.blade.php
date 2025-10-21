@php
    $contacts = \App\Models\ContactImportant::active()->ordered()->get();
@endphp

@if($contacts->count() > 0)
<div class="widget contact-important-widget">
    <div class="widget-header">
        <h4 class="widget-title">
            <i class="fas fa-phone-alt"></i>
            Kontak Penting
        </h4>
    </div>
    
    <div class="widget-content">
        <div class="contact-list">
            @foreach($contacts as $contact)
                <div class="contact-item">
                    <div class="contact-info">
                        <div class="contact-name">
                            <strong>{{ $contact->name }}</strong>
                            <span class="contact-type badge bg-{{ $contact->type == 'polisi' ? 'primary' : ($contact->type == 'rumah_sakit' ? 'danger' : ($contact->type == 'pemadam_kebakaran' ? 'warning' : 'info')) }}">
                                {{ ucwords(str_replace('_', ' ', $contact->type)) }}
                            </span>
                        </div>
                        
                        @if($contact->phone)
                            <div class="contact-phone">
                                <a href="tel:{{ $contact->phone }}" class="phone-link">
                                    <i class="fas fa-phone"></i>
                                    {{ $contact->formatted_phone }}
                                </a>
                            </div>
                        @endif
                        
                        @if($contact->address)
                            <div class="contact-address">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ Str::limit($contact->address, 50) }}
                            </div>
                        @endif
                        
                        @if($contact->description)
                            <div class="contact-description">
                                <small class="text-muted">{{ Str::limit($contact->description, 80) }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="widget-footer">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Hubungi nomor di atas untuk keperluan darurat
            </small>
        </div>
    </div>
</div>

<style>
.contact-important-widget {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.contact-important-widget .widget-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 16px;
}

.contact-important-widget .widget-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.contact-important-widget .widget-title i {
    margin-right: 6px;
    font-size: 12px;
}

.contact-important-widget .widget-content {
    padding: 0;
}

.contact-list {
    max-height: 300px;
    overflow-y: auto;
}

.contact-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-item:hover {
    background-color: #f8f9fa;
}

.contact-name {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}

.contact-name strong {
    color: #333;
    font-size: 13px;
}

.contact-type {
    font-size: 9px;
    padding: 2px 5px;
}

.contact-phone {
    margin-bottom: 4px;
}

.phone-link {
    color: #28a745;
    text-decoration: none;
    font-weight: 500;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    transition: color 0.2s ease;
}

.phone-link:hover {
    color: #218838;
    text-decoration: none;
}

.phone-link i {
    margin-right: 4px;
    font-size: 11px;
}

.contact-address {
    color: #666;
    font-size: 11px;
    margin-bottom: 4px;
    display: flex;
    align-items: flex-start;
}

.contact-address i {
    margin-right: 4px;
    margin-top: 2px;
    color: #999;
    font-size: 10px;
}

.contact-description {
    margin-top: 4px;
}

.widget-footer {
    padding: 8px 16px;
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.widget-footer small {
    color: #6c757d;
    font-size: 10px;
}

.widget-footer i {
    margin-right: 4px;
    font-size: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .contact-important-widget .widget-header {
        padding: 12px 15px;
    }
    
    .contact-item {
        padding: 12px 15px;
    }
    
    .contact-name {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .contact-type {
        align-self: flex-start;
    }
}

/* Scrollbar styling */
.contact-list::-webkit-scrollbar {
    width: 4px;
}

.contact-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.contact-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.contact-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endif
