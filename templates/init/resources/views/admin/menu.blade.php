<router-link tag="li" to="/" exact>
    <a><i class="fa fa-dashboard"></i> 总览</a>
</router-link>

<router-treeview title="用户管理" icon="fa fa-users" :match="['/user']">
    <router-link tag="li" to="/user/list">
        <a><i class="fa fa-user"></i> <span>用户列表</span></a>
    </router-link>
    <router-link tag="li" to="/user/role">
        <a><i class="fa fa-circle-o"></i> <span>用户角色</span></a>
    </router-link>
    <router-link tag="li" to="/user/permission">
        <a><i class="fa fa-circle-o"></i> <span>用户权限</span></a>
    </router-link>
</router-treeview>

<router-link tag="li" to="/upload/list">
    <a><i class="fa fa-upload"></i> <span>上传内容管理</span></a>
</router-link>

<router-link tag="li" to="/actionlog">
    <a><i class="fa fa-circle-o"></i> 操作记录</a>
</router-link>
