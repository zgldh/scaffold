export function {{$functionName}}({{join(', ',$parameters)}}) {
  return request({
    url: `{{$route}}`,
    method: 'get'
    // params
  })
}
