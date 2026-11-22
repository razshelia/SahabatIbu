// kamera: posisi dihitung dari area kosong di samping kartu teks
const SP={
 scan:{side:1,c:[8.5,1.5,9.6],w:2.5,h:3.1,m:{c:[8.5,1.6,9.6],w:2.3,h:2.4}},
 rak:{side:-1,c:[-4.9,2.3,8.2],w:7.6,h:5.6,m:{c:[-4.9,2.2,8.2],w:7.2,h:4.6}},
 timbang:{side:1,c:[5.5,1.7,4.8],w:3.0,h:3.8,m:{c:[5.5,1.5,4.8],w:2.5,h:2.8}},
 struk:{side:-1,c:[-.8,1.4,1.8],w:2.2,h:3.1,m:{c:[-.8,1.3,1.8],w:2.2,h:2.3}},
 riwayat:{side:1,c:[2.8,1.6,-.4],w:4.8,h:3.3,m:{c:[2.8,1.6,-.4],w:3.9,h:2.8}},
 capit:{side:-1,c:[-4.2,2.55,-2.2],w:4.0,h:5.3,m:{c:[-4.2,1.7,-2.2],w:3.9,h:3.2}}};
const GR={scan:pm,rak:rk,timbang:tm,struk:pr,riwayat:bk,capit:cp},SD={scan:1,rak:-1,timbang:1,struk:-1,riwayat:1,capit:-1};
const MQ=matchMedia('(max-width:900px) and (min-height:560px)');
let CH=280;
function region(side){const W=innerWidth,H=innerHeight;
 if(MQ.matches)return{x0:8,x1:W-8,y0:118,y1:Math.max(300,H-CH-14)};
 const sw=0,cw=Math.min(440,W*.42),pad=W*.05;
 return side>0?{x0:sw+pad+cw+30,x1:W-18,y0:86,y1:H-40}:{x0:sw+18,x1:W-pad-cw-30,y0:86,y1:H-40}}
function view(k){const a=innerWidth/innerHeight,ft=.384,W=innerWidth,H=innerHeight;
 if(k=='luar')return{p:[0,5,a<.9?46:32],t:[0,3,14.3]};
 if(k=='home')return{p:[0,3.4,a<.9?16:13.2],t:[0,2,-1]};
 const sp=SP[k],q=MQ.matches&&sp.m?sp.m:sp,rg=region(sp.side),aw=rg.x1-rg.x0,ah=rg.y1-rg.y0;
 const d=Math.max(q.h/(ah/H*2*ft),q.w/(aw/W*2*ft*a));
 const nx=(rg.x0+rg.x1)/W-1,ny=1-(rg.y0+rg.y1)/H;
 const tx_=q.c[0]-nx*ft*a*d,ty=q.c[1]-ny*ft*d,el=Math.min(d*.2,5.8-ty);
 return{p:[tx_,ty+el,q.c[2]+d],t:[tx_,ty,q.c[2]]}}
const KEYS=[['hero','home','Beranda'],['scan','card','Cek Saldo'],['rak','tag','Daftar Harga'],['timbang','scale','Setor Sampah'],['struk','receipt','Struk'],['riwayat','book','Buku Tabungan'],['capit','cash','Ambil Uang'],['aman','shield','Keamanan'],['cta','key','Masuk / Daftar']];
const ORD=['hero','scan','rak','timbang','struk','riwayat','capit','aman','cta'];
const NM={hero:'Luar garasi',scan:'Cek saldo',rak:'Daftar harga',timbang:'Timbang sampah',struk:'Cetak struk',riwayat:'Buku tabungan',capit:'Ambil uang',aman:'Aman & privat',cta:'Masuk / daftar'};
const GL={scan:'Alat bank sampah',aman:'Informasi'};
$('keys').innerHTML=KEYS.map(n=>(GL[n[0]]?`<small class="gl">${GL[n[0]]}</small>`:'')+`<button data-k="${n[0]}" title="${n[2]}" onclick="goSec('${n[0]}');toggleSide(false)"><svg class="i"><use href="#i-${n[1]}"/></svg><span class="t">${n[2]}</span></button>`).join('');
addEventListener('keydown',e=>{if(e.key=='Escape')toggleSide(false)});
function toggleSide(o){if(o===undefined){document.documentElement.classList.toggle('mini');setTimeout(size,320);return}$('side').classList.toggle('open',o);$('scrim').classList.toggle('on',o)}
function nav(){document.querySelectorAll('#keys button,#dots button').forEach(b=>b.classList.toggle('on',b.dataset.k==curSec));$('loc').textContent=NM[curSec]||'';$('ds2').textContent=S.door?'Pintu terbuka':'Pintu tertutup'}
let cur='luar',curSec='hero';
const goSec=k=>$(k).scrollIntoView({behavior:'smooth',block:k=='hero'?'start':'center'});
const step=d=>goSec(ORD[Math.max(0,Math.min(ORD.length-1,ORD.indexOf(curSec)+d))]);
