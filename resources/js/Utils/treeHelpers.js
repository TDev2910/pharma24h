/**
 * Helper functions for converting tree and category data for PrimeVue TreeSelect and Dropdown
 */

/**
 * Chuyển danh sách phẳng (flat list với parent_id) thành cấu trúc cây lồng nhau
 */
export const buildTreeFromFlatList = (items) => {
  const map = {}
  const roots = []

  items.forEach(item => {
    map[item.id] = { ...item, children: [] }
  })

  items.forEach(item => {
    if (item.parent_id && map[item.parent_id]) {
      map[item.parent_id].children.push(map[item.id])
    } else {
      roots.push(map[item.id])
    }
  })

  return roots
}

export const convertToDropdownOptions = (categories) => {
  // Nếu là flat list, build tree trước
  const nestedCategories = categories.length > 0 && !categories[0].children
    ? buildTreeFromFlatList(categories)
    : categories

  const options = []
  const addToOptions = (nodes, level = 0) => {
    nodes.forEach(node => {
      const prefix = '─ '.repeat(level)
      options.push({ label: prefix + node.name, value: node.id })
      if (node.children && node.children.length > 0) addToOptions(node.children, level + 1)
    })
  }
  addToOptions(nestedCategories)
  return options
}

export const convertToTreeNodes = (categories) => {
  const nestedCategories = categories.length > 0 && !categories[0].children
    ? buildTreeFromFlatList(categories)
    : categories

  const mapNodes = (cats) => cats.map(cat => ({
    key: cat.id.toString(),
    label: cat.name,
    data: { id: cat.id, name: cat.name },
    children: cat.children && cat.children.length > 0 ? mapNodes(cat.children) : undefined
  }))

  return mapNodes(nestedCategories)
}

export const convertToTreeSelectNodes = (categories) => {
  // Tự detect: nếu là flat list (không có thuộc tính children) thì build tree trước
  const nestedCategories = categories.length > 0 && categories[0].children === undefined
    ? buildTreeFromFlatList(categories)
    : categories

  const mapNodes = (cats) => cats.map(cat => ({
    key: cat.id.toString(),
    label: cat.name,
    data: cat.id,
    children: cat.children && cat.children.length > 0 ? mapNodes(cat.children) : [],
    selectable: true
  }))

  return mapNodes(nestedCategories)
}

export const flattenTreeNodes = (nodes, level = 0) => {
  let result = []
  nodes.forEach(node => {
    result.push({ ...node, level, expanded: false })
    if (node.children && node.children.length > 0) result = result.concat(flattenTreeNodes(node.children, level + 1))
  })
  return result
}
