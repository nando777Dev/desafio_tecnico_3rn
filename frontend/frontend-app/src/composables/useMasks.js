export default function useMask() {
  /**
   * Formata CPF
   * Exemplo: 12345678900 → 123.456.789-00
   */
  const formatarCPF = (valor) => {
    if (!valor) return ''
    let v = valor.replace(/\D/g, '')
    v = v.replace(/(\d{3})(\d)/, '$1.$2')
    v = v.replace(/(\d{3})(\d)/, '$1.$2')
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2')
    return v
  }

  /**
   * Formata valor monetário
   * Exemplo: 123456 → R$ 1.234,56
   */
  const formatarMoeda = (valor) => {
    if (!valor) return ''
    let v = valor.replace(/\D/g, '')
    v = (v / 100).toFixed(2) + ''
    v = v.replace('.', ',')
    v = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    return 'R$ ' + v
  }

  /**
   * Remove a formatação da moeda
   * Exemplo: "R$ 1.234,56" → 1234.56
   */
  const limparMoeda = (valor) => {
    if (!valor) return 0
    return parseFloat(valor.replace(/[^\d,]/g, '').replace(',', '.')) || 0
  }

  return { formatarCPF, formatarMoeda, limparMoeda }
}
