<router-link tag="li" to="/" exact>
    <a><i class="fa fa-dashboard"></i> 总览</a>
</router-link>

<router-treeview title="用户管理" icon="fa fa-users" :match="['/user']">
    <router-link tag="li" to="/user" exact>
        <a><i class="fa fa-user"></i> 管理员列表</a>
    </router-link>
    <router-link tag="li" to="/user/role">
        <a><i class="fa fa-circle-o"></i> 管理员角色</a>
    </router-link>
    <router-link tag="li" to="/user/permission">
        <a><i class="fa fa-circle-o"></i> 管理员权限</a>
    </router-link>
</router-treeview>

<router-link tag="li" to="/upload" exact>
    <a><i class="fa fa-edit"></i> 上传内容管理</a>
</router-link>

<router-link tag="li" to="/actionlog">
    <a><i class="fa fa-circle-o"></i> 操作记录</a>
</router-link>
