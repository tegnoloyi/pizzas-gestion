/**
 * pos.js — Lógica del Punto de Venta (Sistema Pizzero)
 *
 * Este archivo depende de dos cosas que se cargan ANTES que este script,
 * inyectadas desde resources/views/Ventas/pos.blade.php:
 *
 * 1) El "puente" de base de datos (dbPizzas, dbMariscos, dbBebidas, dbDirectos,
 *    dbPaquetes, dbIngredientes, dbTamanosBase, dbEspecialidades,
 *    dbCategoriasExtras, dbMagnoPrice, dbPreciosOrilla, dbClientes, dbDirecciones)
 *    — se queda inline en el blade porque viene directo de PHP (Bloque 2 del mapa).
 *
 * 2) window.posConfig — objeto con todo lo que antes eran variables de Blade
 *    quemadas en este script (csrf token, rutas con route(), datos de venta_edit
 *    para modo edición, etc.). Se define en un <script> pequeño justo antes de
 *    cargar este archivo. Si agregas un nuevo campo de Laravel aquí, TIENE que
 *    pasar por posConfig — este archivo ya no lleva ninguna sintaxis Blade.
 */

        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                openServicio: false,
                openExtras: false,

                mobilePayOpen: false,
                mobilePayTouchStartY: 0,
                productModalTouchStartY: 0,
                cat: 12, view: 'pizzas', search: '', cart: window.posConfig.cartPreloaded, cartGroups: [], 
                servicio: window.posConfig.ventaEdit.tipoServicio, 
                mesa: window.posConfig.ventaEdit.mesa, 
                nombreClienteMesa: window.posConfig.ventaEdit.nombreClienteMesa,
                id_venta_edit: window.posConfig.ventaEdit.idVentaEdit,
                esAdmin: window.posConfig.esAdmin,
                modalAdminPass: false,
                adminPasswordInput: '',
                adminPassError: '',
                modalCortesia: false,
                _pendingCortesia: null,
                cortesiaPassInput: '',
                cortesiaPassError: '',
                _pendingEsAbierta: false,
                comentariosGenerales: window.posConfig.ventaEdit.comentarios, comentariosGeneralesTemp: '', modalComentarios: false,
                modalOpc: false, opcItem: null,

                cortesia: 0, 

                showIngs: false, tempIngs: [],

                modalPaq1: false, paq1Pizzas: [], paq1MitadesMode: false, paq1Halves: [], paqObj: null,
                modalPaq2: false, paq2Tipo: 'hamb', paq2Extra: '', paq2Pizza: '', paq2MitadesMode: false, paq2MitadesArr: [],
                modalPaq3: false, paq3Pizzas: [], paq3MitadesMode: false, paq3Halves: [], paqTab: 'esp', paqTempMitades: [],
                modalIngredientes: false, ingTam: null, ingSel: [], searchIng: '',ingModo: 'completa',ingMitad1: [],ingMitad2: [],
                modalMitades: false, mitTam: null, mitSel: [],
                modalRectangular: false, rectItem: null, rectSel: [],
                modalBarra: false, barraItem: null, barraSel: [],
                modalBebida: false, bebidaItem: null,
                modalMagno: false, magnoItem: null, magnoSel: [],

                modalTicket: false, ticketVentaId: null, ticketEsEdicion: false,

                modalCliente: false, 
                soloClienteMode: false, // <-- NUEVA VARIABLE
                modalPago: false,
                isProcessing: false,
                searchClienteText: '', showClientesList: false,
                clienteSeleccionado: null, direccionesCliente: [], dirSeleccionada: null,
                clienteFormVisible: false, dirFormVisible: false,
                nuevoClienteData: { nombre: '', apellido: '', telefono: '' }, 
                nuevaDirData: { calle: '', manzana: '', lote: '', colonia: '', referencia: '' },

                pagos: {
                    efectivo: { activo: false, monto: null, entregado: null },
                    tarjeta: { activo: false, monto: null },
                    transferencia: { activo: false, monto: null, referencia: '' }
                },
                
                pagosPreviosRAW: window.posConfig.pagosPreviosRAW,
                total_pagado_previamente: 0,
                domicilioPrevio: window.posConfig.domicilioPrevio,
                pespecialPrevio: window.posConfig.pespecialPrevio,

                init() {
                    if(this.cart && this.cart.length > 0) {
                        this.actualizarCarrito();
                    }
                    
                    if (this.domicilioPrevio) {
                        let clie = dbClientes.find(c => c.id_clie == this.domicilioPrevio.id_clie || c.id_cliente == this.domicilioPrevio.id_clie);
                        if (clie) {
                            this.seleccionarCliente(clie);
                            this.dirSeleccionada = this.domicilioPrevio.id_dir;
                        }
                    }

                    if(this.pagosPreviosRAW && this.pagosPreviosRAW.length > 0) {
                        this.total_pagado_previamente = this.pagosPreviosRAW.reduce((sum, p) => sum + parseFloat(p.monto), 0);
                    }

                    if (this.pespecialPrevio) {
                        let fParts = this.pespecialPrevio.fecha_entrega.split(' ');
                        this.espData.fecha = fParts[0];
                        this.espData.hora = fParts[1] ? fParts[1].substring(0, 5) : '';
                        this.espData.modo = this.pespecialPrevio.id_dir ? 'domicilio' : 'recoger';
                    }
                },

                getListaTamanos() {
                    let d = this.cat === 12 ? dbPizzas : (this.cat === 2 ? dbMariscos : []);
                    if(this.search) d = d.filter(i => i.nombre.toLowerCase().includes(this.search.toLowerCase()));
                    return d;
                },
                getListaDirectos() {
                    let d = dbDirectos.filter(i => i.cat === this.cat);
                    if(this.search) d = d.filter(i => i.nombre.toLowerCase().includes(this.search.toLowerCase()));
                    return d;
                },
                getListaBebidas() {
                    let d = dbBebidas;
                    if(this.search) d = d.filter(i => i.nombre.toLowerCase().includes(this.search.toLowerCase()));
                    return d;
                },
                
                getClienteNombre(cl) {
                    if (!cl) return '';
                    let nom = cl.nombre || cl.Nombre || '';
                    let ape = cl.apellido || cl.Apellido || '';
                    if (!nom && cl.cliente) nom = cl.cliente;
                    return (nom + ' ' + ape).trim() || 'Sin Nombre';
                },

                getClientesFiltrados() {
                    let listaSegura = Array.isArray(dbClientes) ? dbClientes : [];
                    if(!this.searchClienteText || this.searchClienteText.trim() === '') return listaSegura; 
                    let txt = this.searchClienteText.toLowerCase().trim();
                    return listaSegura.filter(c => {
                        let nom = (c.nombre || c.Nombre || '').toLowerCase();
                        let ape = (c.apellido || c.Apellido || '').toLowerCase();
                        let tel = (c.telefono || c.Telefono || '').toLowerCase();
                        let full = (nom + ' ' + ape).trim();
                        return full.includes(txt) || tel.includes(txt);
                    });
                },
                getClienteTelefono(cl) {
                    if (!cl) return '';
                    return cl.telefono || cl.Telefono || cl.celular || cl.numero || 'Sin Teléfono';
                },
                
                abrirOpciones(item) { this.opcItem = item; this.modalOpc = true; },
                abrirBebida(item) { this.bebidaItem = item; this.modalBebida = true; },
                generateUID() { return Math.random().toString(36).substr(2, 9); },

                cleanSize(str) {
                    if (!str) return '';
                    let s = str.toLowerCase();
                    if(s.includes('chica')) return 'Chica';
                    if(s.includes('mediana') || s.includes('media')) return 'Mediana';
                    if(s.includes('grande')) return 'Grande';
                    if(s.includes('familiar')) return 'Familiar';
                    return str; 
                },

                getPrecioOrilla(nombreBase) {
                    let n = nombreBase.toLowerCase();
                    if(n.includes('chica')) return dbPreciosOrilla.chica;
                    if(n.includes('mediana') || n.includes('media')) return dbPreciosOrilla.mediana;
                    if(n.includes('grande')) return dbPreciosOrilla.grande;
                    if(n.includes('familiar')) return dbPreciosOrilla.familiar;
                    return dbPreciosOrilla.chica; 
                },

                fixPrecioOrilla(item) {
                    if (item.precio_orilla && item.precio_orilla > 0) return;
                    if (item.tipo === 'paq') {
                        item.precio_orilla = dbPreciosOrilla.grande;
                    } else if (item.is_magno || item.col === 'id_rec' || item.col === 'id_barr') {
                        item.precio_orilla = dbPreciosOrilla.familiar; 
                    } else {
                        let cTam = this.cleanSize(item.nombre_base);
                        item.precio_orilla = this.getPrecioOrilla(cTam);
                    }
                },

                cartItemTone(item) {
                    if (!item) return 'default';
                    const name = (item.nombre_base || '').toLowerCase();

                    if (item.visual_tone) return item.visual_tone;
                    if (item.tipo === 'paq' || item.col === 'id_paquete') return 'package';
                    if (item.is_magno || item.col === 'id_magno' || name.includes('magno')) return 'magno';
                    if (item.col === 'id_rec' || name.includes('rectangular')) return 'rectangular';
                    if (item.col === 'id_barr' || name.includes('barra')) return 'barra';
                    if (item.col === 'id_refresco' || name.includes('refresco') || name.includes('jarrito')) return 'drink';
                    if (
                        name.includes('alita') ||
                        name.includes('hamburguesa') ||
                        name.includes('costilla') ||
                        name.includes('papa') ||
                        name.includes('espagueti')
                    ) return 'snack';
                    if (item.tipo === 'directo') return 'snack';
                    return 'default';
                },

                cartItemToneColors(item) {
                    const tone = this.cartItemTone(item);
                    const colors = {
                        package: { main: '#ffc107', text: '#111827', soft: '#fff8db', deleteBg: 'rgba(255,255,255,0.22)', deleteText: '#7a5200' },
                        magno: { main: '#343a40', text: '#ffffff', soft: '#f1f5f9', deleteBg: 'rgba(255,255,255,0.16)', deleteText: '#ffffff' },
                        rectangular: { main: '#fd7e14', text: '#ffffff', soft: '#fff4e8', deleteBg: 'rgba(255,255,255,0.18)', deleteText: '#ffffff' },
                        barra: { main: '#17a2b8', text: '#ffffff', soft: '#e9fbfd', deleteBg: 'rgba(255,255,255,0.18)', deleteText: '#ffffff' },
                        drink: { main: '#17a2b8', text: '#ffffff', soft: '#e9fbfd', deleteBg: 'rgba(255,255,255,0.18)', deleteText: '#ffffff' },
                        snack: { main: '#3b82f6', text: '#ffffff', soft: '#eff6ff', deleteBg: 'rgba(255,255,255,0.18)', deleteText: '#ffffff' },
                        default: { main: '#cbd5e1', text: '#212529', soft: '#ffffff', deleteBg: '#fee2e2', deleteText: '#dc3545' }
                    };
                    return colors[tone] || colors.default;
                },

                cartItemShellStyle(item) {
                    const colors = this.cartItemToneColors(item);
                    return {
                        borderColor: colors.main,
                        backgroundColor: colors.soft
                    };
                },

                cartItemHeaderStyle(item) {
                    return {
                        backgroundColor: this.cartItemToneColors(item).main
                    };
                },

                cartItemHeaderTextStyle(item) {
                    return {
                        color: this.cartItemToneColors(item).text
                    };
                },

                cartItemDeleteStyle(item) {
                    const colors = this.cartItemToneColors(item);
                    return {
                        backgroundColor: colors.deleteBg,
                        color: colors.deleteText
                    };
                },

                pizzaPairTone(group) {
                    const items = Array.isArray(group?.items) ? group.items : [];
                    const hasMitades = items.some(entry => {
                        const item = entry?.item || {};
                        const name = (item.nombre_base || '').toLowerCase();
                        return item.tipo === 'piz_mitad' || name.includes('mitad y mitad');
                    });
                    return hasMitades ? 'mitades' : 'default';
                },

                pizzaPairToneColors(group) {
                    const palette = {
                        mitades: { main: '#dc3545', text: '#ffffff', deleteBg: 'rgba(255,255,255,0.18)', deleteText: '#ffffff', border: '#dc3545' },
                        default: { main: '#fbbf24', text: '#111827', deleteBg: 'rgba(255,255,255,0.22)', deleteText: '#7a5200', border: '#fbbf24' }
                    };
                    return palette[this.pizzaPairTone(group)] || palette.default;
                },

                pizzaPairShellStyle(group) {
                    const colors = this.pizzaPairToneColors(group);
                    return {
                        borderColor: colors.border
                    };
                },

                pizzaPairHeaderStyle(group) {
                    return {
                        backgroundColor: this.pizzaPairToneColors(group).main
                    };
                },

                pizzaPairHeaderTextStyle(group) {
                    return {
                        color: this.pizzaPairToneColors(group).text
                    };
                },

                pizzaPairDeleteStyle(group) {
                    const colors = this.pizzaPairToneColors(group);
                    return {
                        backgroundColor: colors.deleteBg,
                        color: colors.deleteText
                    };
                },

                pizzaPairHeaderLabel(group) {
                    return this.pizzaPairTone(group) === 'mitades'
                        ? 'Mitad y Mitad ' + group.size
                        : 'Pizzas ' + group.size;
                },

                cartItemHeaderLabel(item) {
                    if (!item) return '';
                    if (item.is_magno || item.col === 'id_magno') return 'Magno';
                    if (item.col === 'id_rec') return 'Pizza Rectangular';
                    if (item.col === 'id_barr') return 'Pizza de Barra';
                    if (item.tipo === 'paq') return item.nombre_base;
                    return item.nombre_base;
                },

                actualizarCarrito() {
                    let pizzasFlat = [];
                    let normals = [];

                    this.cart.forEach((cItem, index) => {
                        if (cItem.es_pizza && !cItem.is_magno) { 
                            let baseSize = this.cleanSize(cItem.nombre_base).toUpperCase();
                            cItem.subtotalBase = cItem.precioBase;
                            cItem.subtotal = cItem.precioBase + (cItem.orilla_queso ? cItem.precio_orilla : 0);
                            cItem.descuentoPromo = 0;
                            cItem.precioFinal = cItem.precioBase;

                            if (baseSize !== '') {
                                for (let i = 0; i < cItem.qty; i++) {
                                    let cloneUID = cItem.uid + '_' + i;
                                    pizzasFlat.push({ cartIndex: index, size: baseSize, price: cItem.precioBase, item: { ...cItem, unique_key: cloneUID } });
                                }
                            }
                        } else {
                            cItem.subtotalBase = cItem.precioBase * cItem.qty;
                            let extraOrillasPaq = (cItem.orillas_qty || 0) * (cItem.precio_orilla || 0) * cItem.qty;
                            let extraOrillaUnica = (cItem.orilla_queso ? cItem.precio_orilla * cItem.qty : 0);
                            cItem.subtotal = cItem.subtotalBase + extraOrillaUnica + extraOrillasPaq;
                            cItem.descuentoPromo = 0;
                            cItem.precioFinal = cItem.precioBase + (cItem.orilla_queso ? cItem.precio_orilla : 0) + ((cItem.orillas_qty || 0) * (cItem.precio_orilla || 0));
                            normals.push({ cartIndex: index, item: cItem });
                        }
                    });

                    let grouped = pizzasFlat.reduce((acc, p) => {
                        acc[p.size] = acc[p.size] || [];
                        acc[p.size].push(p);
                        return acc;
                    }, {});

                    this.cartGroups = [];

                    for (let size in grouped) {
                        let pArr = grouped[size];
                        pArr.sort((a, b) => b.price - a.price);

                        if (!window.posConfig.promo2x1Activa) {
                            // Promo apagada desde el panel de Promociones: cada pizza se
                            // cobra completa, sin emparejar ni aplicar el 40% a la impar.
                            pArr.forEach(p => {
                                p.item.precioCobrado = p.price;
                                p.item.precioFinal = p.price + (p.item.orilla_queso ? p.item.precio_orilla : 0);
                                let subGroup = p.price + (p.item.orilla_queso ? p.item.precio_orilla : 0);
                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', size: this.cleanSize(size), items: [p], subtotal: subGroup });
                            });
                            continue;
                        }

                        for (let i = 0; i < pArr.length; i += 2) {
                            let p1 = pArr[i];
                            let p2 = pArr[i + 1];

                            let groupItems = [p1];
                            let subGroup = p1.price + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);
                            p1.item.precioCobrado = p1.price;
                            p1.item.precioFinal = p1.item.precioCobrado + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);

                            if (p2) {
                                p2.item.descuentoPromo += p2.price; 
                                p2.item.subtotal -= p2.price;
                                p2.item.precioCobrado = 0;
                                p2.item.precioFinal = 0 + (p2.item.orilla_queso ? p2.item.precio_orilla : 0);
                                subGroup += (p2.item.orilla_queso ? p2.item.precio_orilla : 0);
                                groupItems.push(p2);
                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', size: this.cleanSize(size), items: groupItems, subtotal: subGroup });
                            } else {
                                let desc = p1.price * 0.40;
                                p1.item.descuentoPromo += desc;
                                p1.item.subtotal -= desc;
                                p1.item.precioCobrado = p1.price - desc;
                                p1.item.precioFinal = p1.item.precioCobrado + (p1.item.orilla_queso ? p1.item.precio_orilla : 0);
                                subGroup -= desc;
                                this.cartGroups.push({ id_grupo: this.generateUID(), type: 'pizza_pair', size: this.cleanSize(size), items: groupItems, subtotal: subGroup });
                            }
                        }
                    }

                    normals.forEach(n => {
                        this.cartGroups.push({
                            id_grupo: this.generateUID(),
                            type: 'normal', cIdx: n.cartIndex, item: n.item, subtotal: n.item.subtotal
                        });
                    });
                },

                getCartAnimationTarget() {
                    const cartPanel = this.$refs.cartPanel;
                    const payTarget = this.$refs.payTarget;
                    const isVisible = (el) => {
                        if (!el) return false;
                        const rect = el.getBoundingClientRect();
                        return rect.width > 0 && rect.height > 0 && rect.bottom > 0 && rect.right > 0 && rect.top < window.innerHeight && rect.left < window.innerWidth;
                    };

                    if (isVisible(cartPanel)) return cartPanel;
                    if (isVisible(payTarget)) return payTarget;
                    return cartPanel || payTarget || null;
                },

                animateToCart(event, label = 'Agregado') {
                    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                    const source = event?.currentTarget || event?.target;
                    const target = this.getCartAnimationTarget();
                    if (!source || !target) return;

                    const sourceRect = source.getBoundingClientRect();
                    const targetRect = target.getBoundingClientRect();
                    if (!sourceRect.width || !sourceRect.height || !targetRect.width || !targetRect.height) return;

                    const flyer = document.createElement('div');
                    flyer.className = 'pos-cart-flyer';
                    flyer.textContent = '+ ' + label;
                    document.body.appendChild(flyer);

                    const flyerRect = flyer.getBoundingClientRect();
                    const startX = sourceRect.left + sourceRect.width / 2 - flyerRect.width / 2;
                    const startY = sourceRect.top + sourceRect.height / 2 - flyerRect.height / 2;
                    const endX = targetRect.left + targetRect.width / 2 - flyerRect.width / 2;
                    const endY = targetRect.top + Math.min(targetRect.height / 2, 70) - flyerRect.height / 2;
                    const liftY = Math.min(startY, endY) - 45;

                    if (!flyer.animate) {
                        flyer.style.transform = `translate3d(${endX}px, ${endY}px, 0) scale(0.55)`;
                        flyer.style.opacity = '0';
                        setTimeout(() => flyer.remove(), 280);
                        return;
                    }

                    flyer.animate([
                        { transform: `translate3d(${startX}px, ${startY}px, 0) scale(1)`, opacity: 0.95 },
                        { transform: `translate3d(${(startX + endX) / 2}px, ${liftY}px, 0) scale(0.92)`, opacity: 0.9, offset: 0.55 },
                        { transform: `translate3d(${endX}px, ${endY}px, 0) scale(0.55)`, opacity: 0 }
                    ], {
                        duration: 680,
                        easing: 'cubic-bezier(.22,.8,.25,1)',
                        fill: 'forwards'
                    }).onfinish = () => flyer.remove();

                    if (target.animate) {
                        target.animate([
                            { boxShadow: '0 0 0 rgba(255, 193, 7, 0)' },
                            { boxShadow: '0 0 0 5px rgba(255, 193, 7, 0.28)' },
                            { boxShadow: '0 0 0 rgba(255, 193, 7, 0)' }
                        ], {
                            duration: 520,
                            easing: 'ease-out'
                        });
                    }
                },

                addPizzaToMainCart(obj, event = null, label = 'Pizza') {
                    this.cart.push({ ...obj, qty: 1, uid: this.generateUID() });
                    this.actualizarCarrito();
                    this.animateToCart(event, label);
                },

                addOpc(t, event = null) {
                    let cTam = this.cleanSize(t.tamano);
                    let nomFull = (this.cat === 12 ? 'Pizza ' : 'Mariscos ') + cTam;
                    this.addPizzaToMainCart({
                        db_id: t.id, col: (this.cat === 12 ? 'id_pizza' : 'id_maris'), tipo: 'pizza_normal', es_pizza: true, is_magno: false,
                        nombre_base: nomFull, variante: this.opcItem.nombre, precioBase: parseFloat(t.precio),
                        orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam)
                    }, event, this.opcItem.nombre);
                    this.modalOpc = false;
                },

                addBebida(opc, event = null) {
                    let nomFull = this.bebidaItem.nombre + ' ' + opc.tamano;
                    let idx = this.cart.findIndex(i => i.db_id === opc.id && i.col === 'id_refresco' && !i.is_old);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { this.cart.push({ db_id: opc.id, col: 'id_refresco', tipo: 'directo', nombre_base: nomFull, variante: '', precioBase: parseFloat(opc.precio), qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID() }); }
                    this.actualizarCarrito();
                    this.animateToCart(event, nomFull);
                    this.modalBebida = false;
                },

                abrirMagnoGeneral() {
                    let precioMagno = dbMagnoPrice && dbMagnoPrice > 150 ? parseFloat(dbMagnoPrice) : 260.00; 
                    this.magnoItem = { id: null, col: 'id_pizza', nombre: 'Magno', precio: precioMagno };
                    this.magnoSel = [];
                    this.showIngs = false; 
                    this.tempIngs = [];
                    this.modalMagno = true;
                },
                addMagnoEsp(esp) { if(this.magnoSel.length < 2) this.magnoSel.push(esp); },
                removeMagnoEsp(index) { this.magnoSel.splice(index, 1); },
                formatearMagnoPreview() {
                    if(this.magnoSel.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.magnoSel.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + '/2 ' + k); }
                    return parts.join(' / ');
                },
                addMagno(event = null) {
                    let pb = parseFloat(this.magnoItem.precio);
                    let varianteFinal = this.formatearMagnoPreview();
                    let idx = this.cart.findIndex(i => i.is_magno && i.variante === varianteFinal && !i.orilla_queso && !i.is_old);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { this.cart.push({ db_id: null, col: 'id_magno', tipo: 'directo', nombre_base: 'Magno', variante: varianteFinal, medios: this.magnoSel, precioBase: pb, qty: 1, es_pizza: false, is_magno: true, orilla_queso: false, precio_orilla: dbPreciosOrilla.familiar, uid: this.generateUID() }); }
                    this.actualizarCarrito();
                    this.animateToCart(event, 'Magno');
                    this.modalMagno = false;
                },

                abrirRectangularGeneral() {
                    let baseItem = dbDirectos.find(d => d.cat === 11);
                    if(!baseItem) return showToast('No hay pizzas rectangulares configuradas en la base de datos.');
                    this.rectItem = { id: baseItem.id, col: baseItem.col, nombre: 'Pizza Rectangular', precio: baseItem.precio };
                    this.rectSel = [];
                    this.showIngs = false; 
                    this.tempIngs = [];
                    this.modalRectangular = true;
                },
                addRectEsp(esp) { if(this.rectSel.length < 4) this.rectSel.push(esp); },
                removeRectEsp(index) { this.rectSel.splice(index, 1); },
                formatearCuartosPreview() {
                    if(this.rectSel.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.rectSel.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + '/4 ' + k); }
                    return parts.join(', ');
                },
                addRectangular(event = null) {
                    let pb = parseFloat(this.rectItem.precio);
                    let varianteFinal = this.formatearCuartosPreview();
                    let idx = this.cart.findIndex(i => i.db_id === this.rectItem.id && i.variante === varianteFinal && !i.is_old);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { this.cart.push({ db_id: this.rectItem.id, col: this.rectItem.col, tipo: 'directo', nombre_base: this.rectItem.nombre, variante: varianteFinal, cuartos: this.rectSel, precioBase: pb, qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID(), orilla_queso: false, precio_orilla: dbPreciosOrilla.familiar }); }
                    this.actualizarCarrito();
                    this.animateToCart(event, this.rectItem.nombre);
                    this.modalRectangular = false;
                },

                addBarra(event = null) {
                    let pb = parseFloat(this.barraItem.precio);
                    let varianteFinal = this.formatearMediosPreview();
                    let idx = this.cart.findIndex(i => i.db_id === this.barraItem.id && i.variante === varianteFinal && !i.is_old);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { this.cart.push({ db_id: this.barraItem.id, col: this.barraItem.col, tipo: 'directo', nombre_base: this.barraItem.nombre, variante: varianteFinal, medios: this.barraSel, precioBase: pb, qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID(), orilla_queso: false, precio_orilla: dbPreciosOrilla.familiar }); }
                    this.actualizarCarrito();
                    this.animateToCart(event, this.barraItem.nombre);
                    this.modalBarra = false;
                },

                abrirBarraGeneral() {
                    let baseItem = dbDirectos.find(d => d.cat === 10);
                    if(!baseItem) return showToast('No hay pizzas de barra configuradas en la base de datos.');
                    this.barraItem = { id: baseItem.id, col: baseItem.col, nombre: 'Pizza de Barra', precio: baseItem.precio };
                    this.barraSel = [];
                    this.showIngs = false; 
                    this.tempIngs = [];
                    this.modalBarra = true;
                },
                addBarraEsp(esp) { if(this.barraSel.length < 2) this.barraSel.push(esp); },
                removeBarraEsp(index) { this.barraSel.splice(index, 1); },
                formatearMediosPreview() {
                    if(this.barraSel.length === 0) return 'Sin especialidades';
                    let counts = {};
                    this.barraSel.forEach(x => counts[x] = (counts[x] || 0) + 1);
                    let parts = [];
                    for(let k in counts) { parts.push(counts[k] + '/2 ' + k); }
                    return parts.join(', ');
                },

                addDirecto(p, event = null) {
                    if(p.cat === 11) return this.abrirRectangularGeneral();
                    if(p.cat === 10) return this.abrirBarraGeneral();

                    let idx = this.cart.findIndex(i => i.db_id === p.id && i.col === p.col && !i.es_pizza && !i.is_old);
                    if(idx > -1) { this.cart[idx].qty++; } 
                    else { this.cart.push({ db_id: p.id, col: p.col, tipo: 'directo', nombre_base: p.nombre, variante: '', precioBase: parseFloat(p.precio), qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID(), visual_tone: 'snack' }); }
                    this.actualizarCarrito();
                    this.animateToCart(event, p.nombre);
                },

                abrirPaquete(id) {
                    this.paqObj = dbPaquetes.find(p => p.id_paquete === id);
                    this.paqTab = 'esp'; // Seleccionamos la pestaña de "Enteras" por defecto
                    this.showIngs = false; 
                    this.tempIngs = [];
                    
                    if(id === 1) { 
                        this.paq1Pizzas = []; 
                        this.paq1MitadesMode = false; 
                        this.paq1Halves = []; 
                        this.modalPaq1 = true; 
                    }
                    if(id === 2) { 
                        this.paq2Tipo = 'hamb'; 
                        this.paq2Extra = ''; 
                        this.paq2Pizza = ''; 
                        this.paq2MitadesMode = false; 
                        this.paq2MitadesArr = []; 
                        this.modalPaq2 = true; 
                    }
                    if(id === 3) { 
                        this.paq3Pizzas = []; 
                        this.paqTempMitades = []; 
                        this.modalPaq3 = true; 
                    }
                },

                addPaq(id, pizzas_arr, extra_str, event = null) {
                    let pb = parseFloat(this.paqObj.precio);
                    let maxPizzas = id === 1 ? 2 : (id === 2 ? 1 : 3);
                    this.cart.push({ db_id: id, col: 'id_paquete', tipo: 'paq', nombre_base: 'Paquete '+id, pizzas_paq: pizzas_arr, extra_paq: extra_str, precioBase: pb, qty: 1, es_pizza: false, is_magno: false, uid: this.generateUID(), orillas_qty: 0, max_orillas: maxPizzas, precio_orilla: dbPreciosOrilla.grande }); 
                    this.actualizarCarrito();
                    this.animateToCart(event, 'Paquete ' + id);
                },

                addPaq1Esp(esp) {
                    if(this.paq1MitadesMode) { if(this.paq1Halves.length < 4) this.paq1Halves.push(esp); } 
                    else { if(this.paq1Pizzas.length < 2) this.paq1Pizzas.push(esp); }
                },
                removePaq1Esp(index) {
                    if(this.paq1MitadesMode) this.paq1Halves.splice(index, 1);
                    else this.paq1Pizzas.splice(index, 1);
                },
                addPaq1(event = null) {
                    let pizzasFinales = [];
                    if(this.paq1MitadesMode) {
                        if(this.paq1Halves.length < 4) return showToast("Por favor selecciona las 4 mitades.");
                        pizzasFinales = [{nombre: this.paq1Halves[0] + ' / ' + this.paq1Halves[1], orilla: false}, {nombre: this.paq1Halves[2] + ' / ' + this.paq1Halves[3], orilla: false}];
                    } else {
                        if(this.paq1Pizzas.length < 2) return showToast("Por favor selecciona las 2 pizzas completas.");
                        pizzasFinales = this.paq1Pizzas.map(p => ({nombre: p, orilla: false}));
                    }
                    this.addPaq(1, pizzasFinales, '', event);
                    this.paq1Pizzas = []; this.paq1Halves = [];
                    this.modalPaq1 = false; 
                },

                addPaq2Esp(esp) {
                    if (this.paq2MitadesMode) { if (this.paq2MitadesArr.length < 2) this.paq2MitadesArr.push(esp); } 
                    else { this.paq2Pizza = esp; }
                },
                removePaq2Mitad(index) { this.paq2MitadesArr.splice(index, 1); },
                addPaq2(event = null) {
                    let nombrePizza = this.paq2MitadesMode ? (this.paq2MitadesArr[0] + ' / ' + this.paq2MitadesArr[1]) : this.paq2Pizza;
                    let pizzas = [{nombre: nombrePizza, orilla: false}];
                    this.addPaq(2, pizzas, this.paq2Extra, event);
                    this.modalPaq2 = false; 
                },

                addPaq3(event = null) {
                    if (this.paq3Pizzas.length < 3) return showToast("Por favor agrega las 3 pizzas al paquete.");
                    
                    let pizzasFinales = this.paq3Pizzas.map(p => ({nombre: p, orilla: false}));
                    
                    this.addPaq(3, pizzasFinales, '', event);
                    this.paq3Pizzas = []; 
                    this.paqTempMitades = [];
                    this.modalPaq3 = false; 
                },

                recalcPaqOrillas(item) {
                    item.orillas_qty = item.pizzas_paq.filter(p => p.orilla).length;
                    if(item.orillas_qty > 0) this.fixPrecioOrilla(item);
                    if(item.is_old) {
                        item.is_old = false;
                    }
                    this.actualizarCarrito();
                },

                toggleMitad(nom) { 
                    let idx = this.mitSel.indexOf(nom); 
                    if(idx > -1) this.mitSel.splice(idx, 1); 
                    else if(this.mitSel.length < 2) this.mitSel.push(nom); 
                },
                addMitadEsp(esp) { 
                    if(this.mitSel.length < 2) this.mitSel.push(esp); 
                },
                addMitad(event = null) {
                    let cTam = this.cleanSize(this.mitTam.tamano);
                    let nomFull = 'Mitad y Mitad ' + cTam;
                    this.addPizzaToMainCart({ db_id: null, col: 'id_pizza', tipo: 'piz_mitad', nombre_base: nomFull, variante: this.mitSel[0] + ' / ' + this.mitSel[1], precioBase: parseFloat(this.mitTam.precio), es_pizza: true, is_magno: false, orilla_queso: false, precio_orilla: this.getPrecioOrilla(cTam), mitad1: this.mitSel[0], mitad2: this.mitSel[1], tamano: this.mitTam.tamano }, event, nomFull);
                    this.modalMitades = false; this.mitTam = null; this.mitSel = [];
                },
                
                getIngredientesFiltrados() {
                    if(!this.searchIng || this.searchIng.trim() === '') return dbIngredientes;
                    let s = this.searchIng.toLowerCase().trim();
                    return dbIngredientes.filter(i => (i.ingrediente || '').toLowerCase().includes(s));
                },

                abrirModalIngredientes() {
                    this.ingTam = null;
                    this.ingSel = [];
                    this.ingMitad1 = [];
                    this.ingMitad2 = [];
                    this.ingModo = 'completa';
                    this.searchIng = '';
                    this.modalIngredientes = true;
                },

                ingresoValido() {
                    if (!this.ingTam) return false;
                    if (this.ingModo === 'completa' && this.ingSel.length === 0) return false;
                    if (this.ingModo !== 'completa' && this.ingMitad1.length === 0 && this.ingMitad2.length === 0) return false;
                    return true;
                },

                addIngrediente(event = null) {
                    if(!this.ingresoValido()) return;

                    let strVariante = '';
                    let extrasArray = [];

                    if (this.ingModo === 'completa') {
                        strVariante = this.ingSel.join(', ');
                        extrasArray = this.ingSel;
                    } else {
                        let m1 = this.ingMitad1.length > 0 ? this.ingMitad1.join(', ') : 'QUESO';
                        let m2 = this.ingMitad2.length > 0 ? this.ingMitad2.join(', ') : 'QUESO';
                        
                        strVariante = m1 + ' / ' + m2;
                        extrasArray = [strVariante];
                    }

                    this.cart.push({
                        tipo: 'piz_ing', 
                        es_pizza: true,
                        nombre_base: this.ingTam.tamano,
                        variante: strVariante,
                        precioBase: parseFloat(this.ingTam.precio), 
                        qty: 1, 
                        uid: this.generateUID(),
                        orilla_queso: false, 
                        precio_orilla: this.getPrecioOrilla(this.ingTam.tamano),
                        ingredientes_extra: extrasArray
                    });
                    
                    this.actualizarCarrito();
                    this.animateToCart(event, 'Personalizada');
                    this.modalIngredientes = false;
                },

                recalc() { this.actualizarCarrito(); },
                toggleOrilla(uid, checked) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1) {
                        this.cart[idx].orilla_queso = checked;
                        this.cart[idx].orillas_qty = checked ? this.cart[idx].qty : 0; 
                        
                        if(checked) this.fixPrecioOrilla(this.cart[idx]); 

                        if(this.cart[idx].is_old) {
                            this.cart[idx].is_old = false;
                        }
                    }
                    this.actualizarCarrito();
                },
                
                eliminarItemByUid(uid) {
                    let idx = this.cart.findIndex(c => c.uid === uid);
                    if(idx > -1) {
                        if (this.cart[idx].es_pizza && !this.cart[idx].is_magno && this.cart[idx].qty > 1) {
                            this.cart[idx].qty--;
                        } else {
                            this.cart.splice(idx, 1);
                        }
                    }
                    this.actualizarCarrito();
                },

                eliminarGrupo(group) {
                    group.items.forEach(p => {
                        let idx = this.cart.findIndex(c => c.uid === p.item.uid);
                        if (idx > -1) {
                            if (this.cart[idx].qty > 1) {
                                this.cart[idx].qty--;
                            } else {
                                this.cart.splice(idx, 1);
                            }
                        }
                    });
                    this.actualizarCarrito();
                },

                updateNormalQty(item, mod) {
                    // Si el usuario quiere sumar (+) un artículo que ya fue enviado a cocina (old)
                    if(item.is_old && mod > 0) {
                        let clone = JSON.parse(JSON.stringify(item));
                        clone.qty = 1;
                        clone.is_old = false; // Lo marcamos como NUEVO
                        clone.uid = this.generateUID();
                        
                        // Si ya habíamos agregado uno nuevo igual, le sumamos a ese
                        let idxNuevo = this.cart.findIndex(c => c.db_id === clone.db_id && c.col === clone.col && c.variante === clone.variante && !c.is_old);
                        if (idxNuevo > -1 && clone.tipo === 'directo') {
                            this.cart[idxNuevo].qty += 1;
                        } else {
                            this.cart.push(clone);
                        }
                        this.actualizarCarrito();
                        return;
                    }

                    // Comportamiento normal (restar, o sumar si es nuevo)
                    let idx = this.cart.findIndex(c => c.uid === item.uid);
                    if(idx > -1) {
                        this.cart[idx].qty += mod;
                        if(this.cart[idx].qty < 1) this.cart[idx].qty = 1;
                    }
                    this.actualizarCarrito();
                },

                abrirModalComentarios() { this.comentariosGeneralesTemp = this.comentariosGenerales; this.modalComentarios = true; },
                guardarComentarios() { this.comentariosGenerales = this.comentariosGeneralesTemp; this.modalComentarios = false; },
                
                getTotal() { return this.cartGroups.reduce((s, g) => s + g.subtotal, 0); },
                getGranTotal() {
                    let t = this.getTotal();
                    let descontado = t - (t * (this.cortesia / 100));
                    return this.cortesia > 0 ? Math.ceil(descontado) : descontado;
                },
                
                nomServicio() { 
                    if(this.servicio === 1) return 'Comer Aqui';
                    if(this.servicio === 2) return 'Para Llevar';
                    if(this.servicio === 3) return 'A Domicilio';
                    if(this.servicio === 4) return 'Pedido Especial'; 
                    return 'Seleccionar'; 
                },

                // --- MODIFICADO: AHORA RECIBE EL PARÁMETRO soloCliente ---
                abrirModalCliente(soloCliente = false) {
                    this.soloClienteMode = soloCliente;
                    this.clienteSeleccionado = null;
                    this.searchClienteText = '';
                    this.direccionesCliente = [];
                    this.dirSeleccionada = null;
                    this.clienteFormVisible = false;
                    this.dirFormVisible = false;
                    this.nuevoClienteData = { nombre: '', apellido: '', telefono: '' }; 
                    this.nuevaDirData = { calle: '', manzana: '', lote: '', colonia: '', referencia: '' };
                    this.modalCliente = true;
                },

                toggleFormNuevoCliente() {
                    this.clienteFormVisible = !this.clienteFormVisible;
                    if(this.clienteFormVisible) {
                        this.clienteSeleccionado = null;
                        this.searchClienteText = '';
                        this.direccionesCliente = [];
                        this.dirSeleccionada = null;
                    }
                },
                seleccionarCliente(cl) {
                    this.clienteSeleccionado = cl;
                    this.searchClienteText = this.getClienteNombre(cl);
                    this.showClientesList = false;
                    this.clienteFormVisible = false;
                    this.dirFormVisible = false;
                    
                    let idClieBuscar = cl.id_cliente || cl.id_clie || cl.id;
                    this.direccionesCliente = dbDirecciones.filter(d => d.id_cliente == idClieBuscar || d.id_clie == idClieBuscar);
                    
                    if(this.direccionesCliente.length > 0) {
                        this.dirSeleccionada = this.direccionesCliente[0].id_direccion || this.direccionesCliente[0].id_dir;
                    } else {
                        this.dirSeleccionada = null;
                    }
                },
                
                // --- NUEVO: VALIDA SOLO CLIENTE ---
                esClienteValido() {
                    if (this.clienteFormVisible) {
                        return this.nuevoClienteData.nombre.trim() !== '' && 
                               this.nuevoClienteData.apellido.trim() !== '' && 
                               this.nuevoClienteData.telefono.trim() !== '';
                    }
                    return this.clienteSeleccionado !== null;
                },
                confirmarSoloCliente() {
                    if (this.esClienteValido()) {
                        this.modalCliente = false;
                        if (this.servicio === 4) this.modalEspecial = true;
                    }
                },

                esDomicilioValido() {
                    if (this.clienteFormVisible) {
                        // AQUÍ TAMBIÉN BLOQUEAMOS SI FALTA EL APELLIDO O TELÉFONO
                        if(!this.nuevoClienteData.nombre || !this.nuevoClienteData.apellido || !this.nuevoClienteData.telefono) return false;
                        
                        if(!this.dirFormVisible && this.direccionesCliente.length === 0) {
                            this.dirFormVisible = true;
                        }
                    } else {
                        if(!this.clienteSeleccionado) return false;
                    }
                    if(this.dirFormVisible) {
                        if(!this.nuevaDirData.calle) return false;
                    } else {
                        if(!this.dirSeleccionada) return false;
                    }
                    return true;
                },
                confirmarDomicilio() {
                    if(this.esDomicilioValido()) {
                        this.modalCliente = false;
                        if (this.servicio === 4) {
                            this.modalEspecial = true;
                        } else {
                            this.abrirModalPago();
                        }
                    }
                },

                abrirModalPago() {
                    this.cortesia = 0; 
                    this.pagos = {
                        efectivo: { activo: false, monto: null, entregado: null },
                        tarjeta: { activo: false, monto: null },
                        transferencia: { activo: false, monto: null, referencia: '' }
                    };
                    this.modalPago = true;
                },
                autoFillPago(tipo) {
                    if(this.pagos[tipo].activo) {
                        let falta = this.faltaPagar();
                        if(falta > 0) {
                            this.pagos[tipo].monto = falta;
                        }
                    } else {
                        this.pagos[tipo].monto = null;
                    }
                },
                autoFillAfterCortesia() {
                    if(this.cortesia === 100) {
                        this.pagos.efectivo.activo = false; this.pagos.efectivo.monto = null; this.pagos.efectivo.entregado = null;
                        this.pagos.tarjeta.activo = false; this.pagos.tarjeta.monto = null;
                        this.pagos.transferencia.activo = false; this.pagos.transferencia.monto = null;
                    } else {
                        ['efectivo', 'tarjeta', 'transferencia'].forEach(t => {
                            if(this.pagos[t].activo) {
                                this.pagos[t].monto = null; 
                                this.autoFillPago(t);
                            }
                        });
                    }
                },
                getTotalPagarInputs() {
                    let pE = this.pagos.efectivo.activo ? parseFloat(this.pagos.efectivo.monto || 0) : 0;
                    let pT = this.pagos.tarjeta.activo ? parseFloat(this.pagos.tarjeta.monto || 0) : 0;
                    let pTr = this.pagos.transferencia.activo ? parseFloat(this.pagos.transferencia.monto || 0) : 0;
                    return pE + pT + pTr;
                },
                
                faltaPagar() {
                    let diff = this.getGranTotal() - this.total_pagado_previamente - this.getTotalPagarInputs();
                    return parseFloat(diff.toFixed(2));
                },
                
                pagosValidos() {
                    if(this.faltaPagar() !== 0) return false;
                    if(this.getGranTotal() === 0) return true; // 100% Cortesía
                    
                    if(this.getTotalPagarInputs() === 0 && this.faltaPagar() === 0) return true;

                    if(!this.pagos.efectivo.activo && !this.pagos.tarjeta.activo && !this.pagos.transferencia.activo) return false;
                    if(this.pagos.transferencia.activo && (!this.pagos.transferencia.referencia || this.pagos.transferencia.referencia.trim() === '')) return false;
                    return true;
                },

                procesarOrden() {
                    if(this.servicio === 1) {
                        if(!this.mesa || !this.nombreClienteMesa.trim()) return showToast("El número de mesa y el nombre del cliente son obligatorios.");
                        this.procesarOrdenFinal(true); 
                    } else if(this.servicio === 2) {
                        if(!this.nombreClienteMesa.trim()) return showToast("El nombre del cliente es obligatorio para llevar.");
                        this.abrirModalPago(); 
                    } else if(this.servicio === 3) {
                        if (this.id_venta_edit && this.domicilioPrevio) {
                            this.abrirModalPago(); 
                        } else {
                            this.abrirModalCliente();
                        }
                    } else if(this.servicio === 4) {
                        this.abrirModalEspecial();
                    }
                },

                confirmarAdminPass() {
                    if (!this.adminPasswordInput) {
                        this.adminPassError = 'Ingresa la contraseña.';
                        return;
                    }
                    this.adminPassError = '';
                    this.modalAdminPass = false;
                    this.procesarOrdenFinal(this._pendingEsAbierta);
                },

                pedirCortesia(valor) {
                    this._pendingCortesia = valor;
                    this.cortesiaPassInput = '';
                    this.cortesiaPassError = '';
                    this.modalCortesia = true;
                },

                confirmarCortesia() {
                    if (!this.cortesiaPassInput.trim()) {
                        this.cortesiaPassError = 'Ingresa la contraseña de administrador.';
                        return;
                    }
                    // Guardamos la contraseña en adminPasswordInput para que
                    // procesarOrdenFinal la incluya en la petición al servidor
                    this.adminPasswordInput = this.cortesiaPassInput;
                    this.cortesia = this._pendingCortesia;
                    this._pendingCortesia = null;
                    this.modalCortesia = false;
                    // $nextTick garantiza que modalPago se reabra DESPUÉS de que Alpine
                    // procese el @click.away del modal de pago (que se dispara al cerrar
                    // el modal de contraseña cerrando el de pago sin querer).
                    this.$nextTick(() => {
                        this.modalPago = true;
                        this.autoFillAfterCortesia();
                    });
                },

procesarOrdenFinal(esAbierta = false) {
    // Se eliminó el bloque if(this.id_venta_edit && !this.esAdmin...) que abría el modal

    if(!esAbierta && !this.pagosValidos()) return;
    this.isProcessing = true;

    let cartPayload = [];
    this.cartGroups.forEach(g => {
        if(g.type === 'pizza_pair') {
            g.items.forEach(p => { cartPayload.push({ ...p.item, precioFinal: p.item.precioFinal, qty: 1 }); });
        } else {
            cartPayload.push({ ...g.item, precioFinal: g.item.precioFinal });
        }
    });

    let pagosToSend = [];
    if(!esAbierta) {
        this.pagosPreviosRAW.forEach(p => {
            pagosToSend.push({ 
                id_metpago: p.id_metpago, 
                monto: p.monto, 
                referencia: p.referencia || '', 
                entregado: p.referencia || p.monto 
            });
        });

        if(this.pagos.efectivo.activo && this.pagos.efectivo.monto > 0) {
            pagosToSend.push({ id_metpago: 2, monto: this.pagos.efectivo.monto, entregado: this.pagos.efectivo.entregado || this.pagos.efectivo.monto });
        }
        if(this.pagos.tarjeta.activo && this.pagos.tarjeta.monto > 0) {
            pagosToSend.push({ id_metpago: 1, monto: this.pagos.tarjeta.monto }); 
        }
        if(this.pagos.transferencia.activo && this.pagos.transferencia.monto > 0) {
            pagosToSend.push({ id_metpago: 3, monto: this.pagos.transferencia.monto, referencia: this.pagos.transferencia.referencia });
        }
    }

    let baseComments = this.comentariosGenerales.replace(/ \| CORTESÍA \d+%/ig, '').replace(/CORTESÍA \d+%/ig, '').replace(/ \| CORTESIA \d+%/ig, '').replace(/CORTESIA \d+%/ig, '').replace(/ \| DESCUENTO \d+%/ig, '').replace(/DESCUENTO \d+%/ig, '').trim();
    if (baseComments.endsWith('|')) baseComments = baseComments.slice(0, -1).trim();
    
    let finalComments = baseComments + (this.cortesia > 0 ? (baseComments ? " | " : "") + "DESCUENTO " + this.cortesia + "%" : "");

    let reqBody = {
        _token: window.posConfig.csrfToken, 
        tipo_servicio: this.servicio, 
        mesa: this.mesa, 
        nombre_cliente: this.nombreClienteMesa,
        comentarios: finalComments, 
        total: this.getGranTotal(), 
        carrito: cartPayload,
        pagos: pagosToSend,
        id_venta_edit: this.id_venta_edit,
        cortesia: this.cortesia,
        admin_password: this.adminPasswordInput
    };

    if(this.servicio === 3) {
        if(this.clienteFormVisible) reqBody.nuevo_cliente = this.nuevoClienteData;
        else reqBody.id_clie = this.clienteSeleccionado ? (this.clienteSeleccionado.id_cliente || this.clienteSeleccionado.id_clie) : null;

        if(this.dirFormVisible) reqBody.nueva_direccion = this.nuevaDirData;
        else reqBody.id_dir = this.dirSeleccionada;
    }

    fetch(window.posConfig.routes.ventasPosStore, {
        method: 'POST', 
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json' 
        },
        body: JSON.stringify(reqBody)
    }).then(async r => {
        if(!r.ok) { throw new Error("Error del servidor: " + r.status); }
        return r.json();
    }).then(res => {
        if(res.success) { 
            if (res.nuevo_cliente) {
                const existeClie = dbClientes.find(c => (c.id_clie || c.id_cliente) == res.nuevo_cliente.id_clie);
                if (!existeClie) {
                    dbClientes.push(res.nuevo_cliente);
                }
            }
            if (res.nueva_direccion) {
                const existeDir = dbDirecciones.find(d => (d.id_dir || d.id_direccion) == res.nueva_direccion.id_dir);
                if (!existeDir) {
                    dbDirecciones.push(res.nueva_direccion);
                }
            }
            this.cart = []; 
            this.actualizarCarrito(); 
            this.mesa = ''; 
            this.nombreClienteMesa = ''; 
            this.comentariosGenerales = '';
            this.comentariosGeneralesTemp = ''; 
            this.cortesia = 0;
            this.modalPago = false;
            
            this.clienteSeleccionado = null;
            this.searchClienteText = '';
            this.direccionesCliente = [];
            this.dirSeleccionada = null;
            this.clienteFormVisible = false;
            this.dirFormVisible = false;

            this.ticketVentaId = res.id_venta;
            this.ticketEsEdicion = !!this.id_venta_edit;
            this.modalTicket = true;
            this.isProcessing = false;
        } else if (res.requiere_admin) {
            this.adminPassError = '';
            this.adminPasswordInput = '';
            this._pendingEsAbierta = esAbierta;
            this.modalAdminPass = true;
            this.isProcessing = false;
        } else {
            showToast("Error al guardar: " + res.message);
            this.isProcessing = false;
        }
    }).catch(e => {
        showToast("Ocurrió un error. Intenta de nuevo. " + e.message);
        this.isProcessing = false;
    });
},

                getTicketPreviewUrl() {
                    if (!this.ticketVentaId) return '';
                    let url = '/venta/pos/ticket/' + this.ticketVentaId + '?preview=1';
                    if (this.ticketEsEdicion) url += '&solo_nuevos=1';
                    return url;
                },
                getTicketPrintUrl() {
                    if (!this.ticketVentaId) return '';
                    let url = '/venta/pos/ticket/' + this.ticketVentaId;
                    if (this.ticketEsEdicion) url += '?solo_nuevos=1';
                    return url;
                },
                imprimirTicketPreview() {
                    const width = 420;
                    const height = 700;
                    const left = (window.screen.width / 2) - (width / 2);
                    const top = (window.screen.height / 2) - (height / 2);
                    window.open(this.getTicketPrintUrl(), 'TicketPizzetos', `width=${width},height=${height},left=${left},top=${top},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`);
                },
                cerrarModalTicket() {
                    this.modalTicket = false;
                    if (this.ticketEsEdicion) {
                        window.location.href = window.posConfig.routes.ventasResume;
                    }
                },

                espData: {
                    fecha: '',
                    hora: '',
                    nombre: '',
                    modo: 'recoger', 
                    anticipo_efectivo: null,
                    anticipo_tarjeta: null,
                    anticipo_transferencia: null,
                    referencia_transferencia: ''
                },

                getAnticipoTotal() {
                    let e = parseFloat(this.espData.anticipo_efectivo) || 0;
                    let t = parseFloat(this.espData.anticipo_tarjeta) || 0;
                    let tr = parseFloat(this.espData.anticipo_transferencia) || 0;
                    return e + t + tr;
                },

                modalEspecial: false,

                isEspValido() {
                    if (!this.espData.fecha || !this.espData.hora) return false;
                    
                    let hasClient = this.clienteSeleccionado || this.clienteFormVisible || this.espData.nombre.trim();
                    if (!hasClient) return false;

                    let requiresDbClient = this.espData.modo === 'domicilio';
                    if (requiresDbClient && !this.clienteSeleccionado && !this.clienteFormVisible) return false;
                    if (requiresDbClient && !this.dirSeleccionada && !this.dirFormVisible) return false;
                    
                    let totalAcumulado = this.getAnticipoTotal() + this.total_pagado_previamente;
                    if (totalAcumulado < 0 || totalAcumulado > this.getGranTotal()) return false;
                    
                    if ((parseFloat(this.espData.anticipo_transferencia) || 0) > 0 && !this.espData.referencia_transferencia.trim()) return false;

                    return true;
                },

                abrirModalEspecial() {
                    if(this.cart.length === 0) return showToast('Agrega productos primero.');
                    this.espData.nombre = this.nombreClienteMesa;
                    this.modalEspecial = true;
                },

                async confirmarEspecial() {
                    if(!this.isEspValido()) return;
                    this.isProcessing = true;
                    
                    this.procesarPedidoEspecialFinal();
                },

                async procesarPedidoEspecialFinal() {
                    let cartPayload = [];
                    this.cartGroups.forEach(g => {
                        if(g.type === 'pizza_pair') {
                            g.items.forEach(p => { cartPayload.push({ ...p.item, precioFinal: p.item.precioFinal, qty: 1 }); });
                        } else {
                            cartPayload.push({ ...g.item, precioFinal: g.item.precioFinal });
                        }
                    });

                    let pagosAnticipo = [];
                    if ((parseFloat(this.espData.anticipo_efectivo) || 0) > 0) pagosAnticipo.push({ id_metpago: 2, monto: parseFloat(this.espData.anticipo_efectivo) });
                    if ((parseFloat(this.espData.anticipo_tarjeta) || 0) > 0) pagosAnticipo.push({ id_metpago: 1, monto: parseFloat(this.espData.anticipo_tarjeta) });
                    if ((parseFloat(this.espData.anticipo_transferencia) || 0) > 0) pagosAnticipo.push({ id_metpago: 3, monto: parseFloat(this.espData.anticipo_transferencia), referencia: this.espData.referencia_transferencia });

                    let finalName = this.espData.nombre;
                    if (this.clienteSeleccionado) finalName = this.getClienteNombre(this.clienteSeleccionado);
                    else if (this.clienteFormVisible) finalName = this.nuevoClienteData.nombre;

                    let reqBody = {
                        _token: window.posConfig.csrfToken,
                        id_venta_edit: this.id_venta_edit,
                        nombre_cliente: finalName,
                        comentarios: this.comentariosGenerales,
                        total: this.getGranTotal(),
                        anticipo: this.getAnticipoTotal(),
                        pagos_anticipo: pagosAnticipo,
                        fecha_entrega: this.espData.fecha + ' ' + this.espData.hora,
                        carrito: cartPayload
                    };

                    if (this.clienteFormVisible) reqBody.nuevo_cliente = this.nuevoClienteData;
                    else reqBody.id_clie = this.clienteSeleccionado ? (this.clienteSeleccionado.id_cliente || this.clienteSeleccionado.id_clie) : null;

                    if (this.espData.modo === 'domicilio') {
                        if (this.dirFormVisible) reqBody.nueva_direccion = this.nuevaDirData;
                        else reqBody.id_dir = this.dirSeleccionada;
                    }

                    try {
                        let response = await fetch(window.posConfig.routes.especialesStore, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(reqBody)
                        });
                        
                        let res = await response.json();
                        if(res.success) {
                            let urlTicket = '/venta/pos/ticket/' + res.id_venta + '?solo_nuevos=1';
                            const w = 420; const h = 700; const l = (window.screen.width/2)-(w/2); const t = (window.screen.height/2)-(h/2);
                            window.open(urlTicket, 'TicketEspecial', `width=${w},height=${h},left=${l},top=${t},menubar=no,toolbar=no,location=no,status=no,scrollbars=yes`);

                            showToast("Pedido Especial #" + res.id_venta + (this.id_venta_edit ? " actualizado" : " guardado"), 'success');
                            if(this.id_venta_edit) {
                                window.location.href = window.posConfig.routes.especialesIndex;
                            } else {
                                window.location.reload(); 
                            }
                        } else {
                            showToast("Error: " + res.message);
                        }
                    } catch (e) {
                        showToast("Error de conexión");
                    } finally {
                        this.isProcessing = false;
                    }
                }

            }));
        });
