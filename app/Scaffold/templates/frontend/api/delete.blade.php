export function {{$functionName}}({{join(', ',$parameters)}}) {
  return request({
    // params,
    url: `{{$route}}`,
    method: 'delete'
  })
}