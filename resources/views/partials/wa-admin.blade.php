@php
    $waNo = preg_replace('/\D+/', '', (string) config('kontak.whatsapp'));
    if ($waNo !== '' && str_starts_with($waNo, '0')) {
        $waNo = '62' . substr($waNo, 1);
    }
    $waUrl = $waNo !== ''
        ? 'https://wa.me/' . $waNo . '?text=' . rawurlencode((string) config('kontak.whatsapp_pesan'))
        : null;
@endphp
<a id="wa-admin" class="g" href="{{ $waUrl ?? '#' }}"
   @if($waUrl) target="_blank" rel="noopener noreferrer" @else onclick="event.preventDefault();var t=document.getElementById('toast');if(t){t.textContent='Nomor admin belum diisi (ADMIN_WHATSAPP di file .env).';t.classList.add('on');setTimeout(function(){t.classList.remove('on')},4000)}" @endif
   aria-label="Hubungi admin lewat WhatsApp">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.04 2a9.9 9.9 0 0 0-8.5 14.9L2 22l5.27-1.38A9.9 9.9 0 1 0 12.04 2Zm0 1.8a8.1 8.1 0 1 1-4.2 15.03l-.3-.18-3.13.82.84-3.05-.2-.31A8.1 8.1 0 0 1 12.04 3.8Zm-3.2 3.9c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.06 3.3 5.1 4.5 2.53 1 3.04.8 3.59.75.55-.05 1.78-.73 2.03-1.43.25-.7.25-1.3.17-1.43-.07-.12-.27-.2-.57-.35-.3-.15-1.78-.88-2.05-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.68-2.08-.17-.3-.02-.46.13-.6.14-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.65-.93-2.25-.24-.58-.5-.5-.68-.5Z"/></svg>
    <span>Hubungi Admin</span>
</a>
