/**
 * Helper functions for converting tree and category data for PrimeVue TreeSelect and Dropdown
 */

export const convertToDropdownOptions = (categories) => {
  const options = []
  const addToOptions = (nodes, level = 0) => {
    nodes.forEach(node => {
      const prefix = '─ '.repeat(level)
      options.push({ label: prefix + node.name, value: node.id })
      if (node.children && node.children.length > 0) addToOptions(node.children, level + 1)
    })
  }
  addToOptions(categories)
  return options
}

export const convertToTreeNodes = (categories) => {
  return categories.map(cat => ({
    key: cat.id.toString(),
    label: cat.name,
    data: { id: cat.id, name: cat.name },
    children: cat.children ? convertToTreeNodes(cat.children) : undefined
  }))
}

export const convertToTreeSelectNodes = (categories) => {
  return categories.map(cat => ({
    key: cat.id.toString(),
    label: cat.name,
    data: cat.id,
    children: cat.children ? convertToTreeSelectNodes(cat.children) : undefined,
    selectable: true
  }))
}

export const flattenTreeNodes = (nodes, level = 0) => {
  let result = []
  nodes.forEach(node => {
    result.push({ ...node, level, expanded: false })
    if (node.children && node.children.length > 0) result = result.concat(flattenTreeNodes(node.children, level + 1))
  })
  return result
}
