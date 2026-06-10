/**
 * Helper central de URLs e fetch para o SGM.
 * Garante caminhos corretos e parsing uniforme das respostas JSON.
 */
(function () {
    function sgmUrl(path) {
        const base = window.SGM_BASE || '';
        if (!path) return base;
        if (/^https?:\/\//i.test(path)) return path;
        const clean = String(path).replace(/^\//, '');
        return base + clean;
    }

    async function sgmFetch(path, options) {
        const res = await fetch(sgmUrl(path), options);
        let json;
        try {
            json = await res.json();
        } catch (e) {
            return { success: false, message: 'Resposta inválida do servidor.', data: null };
        }
        if (!res.ok && json && json.success === undefined) {
            return { success: false, message: json.message || 'Erro na requisição.', data: null };
        }
        return json;
    }

    /** Converte resposta em lista (array) */
    function sgmAsList(json) {
        if (Array.isArray(json)) {
            return { success: true, data: json, message: '' };
        }
        if (json && json.success === false) {
            return { success: false, data: [], message: json.message || 'Erro ao carregar dados.' };
        }
        if (json && Array.isArray(json.data)) {
            return { success: true, data: json.data, message: json.message || '' };
        }
        return { success: false, data: [], message: 'Formato de lista inválido.' };
    }

    /** Converte resposta em objeto único */
    function sgmAsObject(json) {
        if (!json) return null;
        if (json.success === false) return null;
        if (json.data !== undefined && json.data !== null && typeof json.data === 'object' && !Array.isArray(json.data)) {
            return json.data;
        }
        if (json.id_chamado || json.id_usuario || json.id_ambiente) {
            return json;
        }
        if (json.success === true && json.data === null) return null;
        return json.success === undefined ? json : json.data;
    }

    window.sgmUrl = sgmUrl;
    window.sgmFetch = sgmFetch;
    window.sgmAsList = sgmAsList;
    window.sgmAsObject = sgmAsObject;
})();
