// animasi
const ease=t=>t<.5?2*t*t:1-Math.pow(-2*t+2,2)/2;
const tw=(f,ms)=>new Promise(r=>{const t0=performance.now();(function s(){const p=Math.min(1,(performance.now()-t0)/ms);f(ease(p));p<1?requestAnimationFrame(s):r()})()});
const wait=ms=>new Promise(r=>setTimeout(r,ms));
function popPile(j){const g=PILES[j];if(!g)return;tw(p=>{g.position.y=g.by+Math.sin(p*Math.PI)*.4},450)}
async function dropItem(j,k){const m=j=='Plastik'?new THREE.Mesh(new THREE.CylinderGeometry(.22,.22,.8,20),M(J[j].c,.3)):j=='Kertas'?new THREE.Mesh(new THREE.BoxGeometry(.9,.4,.7),M(J[j].c)):new THREE.Mesh(new THREE.CylinderGeometry(.28,.28,.6,20),M(J[j].c,.2,.9));m.castShadow=true;m.position.set(0,3.1,.1);tm.add(m);
 await tw(p=>{m.position.y=3.1-p*2.4},500);await tw(p=>{m.position.y=.7+Math.sin(p*Math.PI)*.2},240);
 const w0=wShow;await tw(p=>{wShow=w0+k*p;tmS.render()},700);await wait(250);tm.remove(m)}
async function printRcpt(r){S.busy=true;lastR=r;rcptDraw(r);redraw();paper.scale.y=.01;await tw(p=>{const s=.01+.99*p;paper.scale.y=s;paper.position.y=1.05+.75*s},1400);S.busy=false;redraw()}
async function doSetor(){if(S.busy||!S.cart.length)return;S.busy=true;S.m.tm='';wShow=0;redraw();await wait(500);
 const rows=[];let tot=0;for(const r of S.cart){await dropItem(r.j,r.kg);const v=r.kg*J[r.j].h;tot+=v;rows.push([r.j+' '+fmt(r.kg)+'kg',Math.round(v).toLocaleString('id-ID'),34])}
 S.saldo+=tot;const k='SET-0'+(S.n++),t='22 Nov';S.riw.unshift({k,t,v:tot,d:S.cart.map(r=>r.j+' '+fmt(r.kg)+' kg').join(', ')});S.cart=[];wShow=0;S.m.tm=''+k+' tercatat';S.busy=false;redraw();
 toast(''+k+' tersimpan! Saldo bertambah '+rp(tot)+'. Struk sedang dicetak.');goSec('struk');await wait(1100);await printRcpt({k,t,rows,v:tot,s:S.saldo})}
async function doCapit(){if(S.busy)return;if(S.nom<=0||S.nom>avail()){S.m.cp='Jumlah harus lebih dari 0 dan sesuai saldo.';toast('Jumlah uang harus lebih dari 0 dan tidak melebihi saldo yang bisa diambil.');redraw();return}
 S.busy=true;S.tahan+=S.nom;const id='TRK-0'+(S.tn++),nm=S.nom;S.tarik.unshift({id,v:S.nom,st:'Menunggu'});S.tab='status';S.nom=Math.min(S.nom,Math.floor(avail()/10000)*10000);redraw();
 toast('Pengajuan '+id+' ('+rp(nm)+') masuk. Uangnya ditahan dulu, status: Menunggu.');
 const e=envs[Math.floor(Math.random()*envs.length)];const ex=e.position.x,ey=e.position.y,ez=e.position.z,x0=claw.position.x,z0=claw.position.z;
 await tw(p=>{claw.position.x=x0+(ex-x0)*p;claw.position.z=z0+(ez-z0)*p},900);await tw(p=>{claw.position.y=CY-p*DY},700);
 await tw(p=>fg.forEach((f,i)=>{f.position.x=Math.cos(i*2.09)*(.22-.14*p);f.position.z=Math.sin(i*2.09)*(.22-.14*p)}),400);
 cp.remove(e);claw.add(e);e.position.set(0,-.85,0);
 await tw(p=>{claw.position.y=CY-DY+p*DY},700);await tw(p=>{claw.position.x=ex+(1.2-ex)*p;claw.position.z=ez+(.2-ez)*p},900);
 claw.remove(e);cp.add(e);e.position.set(1.2,CY-.85,.2);await tw(p=>{e.position.y=CY-.85-p*(CY-.85-2.34)},500);
 await tw(p=>fg.forEach((f,i)=>{f.position.x=Math.cos(i*2.09)*(.08+.14*p);f.position.z=Math.sin(i*2.09)*(.08+.14*p)}),300);
 await tw(p=>{claw.position.x=1.2*(1-p);claw.position.z=.2*(1-p)},600);e.position.set(ex,ey,ez);
 S.busy=false;S.m.cp='Pengajuan '+id+' masuk. Dana ditahan.';redraw()}
function aksi(t,a){if(a=='batal'){S.tahan-=t.v;t.st='Dibatalkan';toast('↩ Pengajuan dibatalkan, uang kembali ke saldo yang bisa diambil.')}else if(t.st=='Menunggu'){t.st='Disetujui';toast('Pengurus menyetujui '+t.id+'. Tinggal ambil uang tunai.')}else{S.saldo-=t.v;S.tahan-=t.v;t.st='Dicairkan';S.riw.unshift({k:t.id,t:'22 Nov',v:-t.v,d:'Penarikan tunai'});toast(''+t.id+' dicairkan '+rp(t.v)+'. Tercatat di Buku Tabungan.')}S.nom=Math.min(S.nom,Math.floor(avail()/10000)*10000);redraw()}

