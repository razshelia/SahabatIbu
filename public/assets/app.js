(()=>{'use strict';
// DEMO FRONT-END: di Laravel semua validasi, throttle, sesi & hash kata sandi dijalankan di SERVER.
const ss=sessionStorage,$=s=>document.querySelector(s);
const V={ada:v=>v?'':'Wajib diisi.',
nama:v=>v&&v.length<=100?'':'Nama wajib, maksimal 100 karakter.',
email:v=>v.length<=255&&/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)?'':'Format email tidak valid.',
telepon:v=>/^[0-9+]{1,20}$/.test(v)?'':'Telepon hanya angka dan +, maksimal 20 karakter.',
alamat:v=>v&&v.length<=255?'':'Alamat wajib, maksimal 255 karakter.',
sandi:v=>/^(?=.*[A-Za-z])(?=.*\d).{8,}$/.test(v)?'':'Minimal 8 karakter, memuat huruf dan angka.',
konf:v=>v===($('#sandi')||{}).value?'':'Konfirmasi kata sandi tidak sama.',
cek:(v,el)=>el.checked?'':'Persetujuan privasi wajib dicentang.'};
const get=()=>{try{return JSON.parse(ss.getItem('user'))}catch(e){return null}},put=u=>ss.setItem('user',JSON.stringify(u));
const go=u=>{location.href=u},msg=(t,ok)=>{const e=$('#fe');e.textContent=t;e.classList.toggle('ok',!!ok)};
const consent=()=>({versi:'1.0',waktu:new Date().toISOString()}); // bukti persetujuan (F-42)
function check(f){let first=null;f.querySelectorAll('[data-v]').forEach(el=>{const m=V[el.dataset.v](el.type=='password'?el.value:el.value.trim(),el),er=$('#e-'+el.id);er.textContent=m;el.setAttribute('aria-invalid',String(!!m));if(m&&!first)first=el});if(first)first.focus();return!first}
const A={
login(f){const n=Date.now(),h=JSON.parse(ss.getItem('hits')||'[]').filter(t=>n-t<6e4);
 if(h.length>=5)return msg('Terlalu banyak percobaan (HTTP 429). Coba lagi sebentar.');
 h.push(n);ss.setItem('hits',JSON.stringify(h));const u=get();
 const em=f.email.value.trim().toLowerCase();if(em.startsWith('admin@'))return go(ss.getItem('pwok')?'admin-dashboard.html':'ganti-kata-sandi.html');if(em.startsWith('ani@'))return go('nasabah-dashboard.html');
 if(u&&u.email===em)return go('status-pendaftaran.html');
 msg('Email atau kata sandi salah.')}, // pesan seragam: tidak membocorkan email terdaftar
daftar(f){put({nama:f.nama.value.trim(),email:f.email.value.trim().toLowerCase(),telepon:f.telepon.value.trim(),alamat:f.alamat.value.trim(),status:'menunggu',persetujuan:consent()});go('status-pendaftaran.html')},
lengkapi(f){put({...(get()||{nama:'Pengguna Google',email:'(akun Google)'}),telepon:f.telepon.value.trim(),alamat:f.alamat.value.trim(),status:'menunggu',persetujuan:consent()});go('status-pendaftaran.html')},
ganti(f){if(f.sandi.value===f.lama.value)return msg('Kata sandi baru harus berbeda dari yang lama.');ss.setItem('pwok','1');go('admin-dashboard.html')},
lupa(){msg('Jika email terdaftar, tautan reset sekali pakai sudah dikirim.',true)}};
document.querySelectorAll('form[data-act]').forEach(f=>f.addEventListener('submit',e=>{e.preventDefault();if(check(f))A[f.dataset.act](f)}));
document.querySelectorAll('[data-google]').forEach(b=>b.addEventListener('click',()=>go('lengkapi-profil.html')));
if($('#st-t')){const u=get();if(u){$('#st-t').textContent='Menunggu Verifikasi';$('#st-n').textContent=u.nama+' · '+u.email}else{$('#st-t').textContent='Belum ada pendaftaran';$('#st-n').textContent='Silakan daftar atau masuk dulu.'}
 $('#out').addEventListener('click',()=>{ss.clear();go('login.html')})}
})();